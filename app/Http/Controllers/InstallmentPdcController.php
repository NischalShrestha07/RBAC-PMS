<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Commitment;
use App\Models\Installment;
use App\Models\PdcInstallment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InstallmentPdcController extends Controller
{

    public function index(Request $request)
    {
        $user = Auth::user();

        $filterDate = $request->get('filter_date', 'all');
        $filterAgent = $request->get('filter_agent', null);
        $filterClient = $request->get('filter_client', null);
        $filterStatus = $request->get('filter_status', null);
        $filterType = $request->get('filter_type', null);
        $filterDepartment = $request->get('filter_department', null);

        // Base query for Installments with proper data scoping
        $installmentsQuery = $this->getAccessibleData(Installment::class)
            ->with('commitment')
            ->select(
                'installments.id',
                'commitments.name as client_name',
                'installments.paid_amount',
                'installments.amount',
                'installments.due_date',
                'installments.status',
                DB::raw("'Commitment' as type"),
                DB::raw("NULL as chequeno"),
                DB::raw("NULL as bankname"),
                DB::raw("NULL as pdc_id")
            )
            ->join('commitments', function ($join) {
                $join->on('installments.commitment_id', '=', 'commitments.id')
                    ->whereNull('commitments.deleted_at');
            })
            ->whereNull('installments.deleted_at');

        // Base query for PDC Installments with proper data scoping
        $pdcInstallmentsQuery = $this->getAccessibleData(PdcInstallment::class)
            ->with(['pdc.commitment'])
            ->select(
                'pdc_installments.id',
                'pdcs.name as client_name',
                'pdc_installments.data1 as paid_amount', // Renamed for consistency
                'pdc_installments.amount',
                'pdc_installments.due_date',
                'pdc_installments.status',
                DB::raw("'PDC' as type"),
                'pdc_installments.chequeno',
                'pdc_installments.bankname',
                'pdc_installments.pdc_id'
            )
            ->join('pdcs', function ($join) {
                $join->on('pdc_installments.pdc_id', '=', 'pdcs.id')
                    ->whereNull('pdcs.deleted_at');
            })
            ->whereNull('pdc_installments.deleted_at');

        // Apply department filter if specified
        if ($filterDepartment) {
            $installmentsQuery->where('commitments.department_id', $filterDepartment);
            $pdcInstallmentsQuery->where('pdcs.department_id', $filterDepartment);
        }

        // Date filtering
        if ($filterDate !== 'all') {
            $startDate = now();
            $endDate = now();

            if ($filterDate == 'today') {
                $installmentsQuery->whereDate('installments.due_date', $startDate);
                $pdcInstallmentsQuery->whereDate('pdc_installments.due_date', $startDate);
            } elseif ($filterDate == 'this_week') {
                $installmentsQuery->whereBetween('installments.due_date', [$startDate->startOfWeek(), $startDate->endOfWeek()]);
                $pdcInstallmentsQuery->whereBetween('pdc_installments.due_date', [$startDate->startOfWeek(), $startDate->endOfWeek()]);
            } elseif ($filterDate == 'this_month') {
                $installmentsQuery->whereBetween('installments.due_date', [$startDate->startOfMonth(), $startDate->endOfMonth()]);
                $pdcInstallmentsQuery->whereBetween('pdc_installments.due_date', [$startDate->startOfMonth(), $startDate->endOfMonth()]);
            } elseif ($filterDate == 'custom') {
                $startDate = $request->get('start_date');
                $endDate = $request->get('end_date');
                if ($startDate && $endDate) {
                    $installmentsQuery->whereBetween('installments.due_date', [$startDate, $endDate]);
                    $pdcInstallmentsQuery->whereBetween('pdc_installments.due_date', [$startDate, $endDate]);
                }
            }
        }

        // Status filter
        if ($filterStatus) {
            $installmentsQuery->where('installments.status', $filterStatus);
            $pdcInstallmentsQuery->where('pdc_installments.status', $filterStatus);
        }

        // Client filter
        if ($filterClient) {
            $installmentsQuery->where('commitments.name', 'like', '%' . $filterClient . '%');
            $pdcInstallmentsQuery->where('pdcs.name', 'like', '%' . $filterClient . '%');
        }

        // Agent filter
        if ($filterAgent) {
            $installmentsQuery->whereHas('commitment.agent', function ($q) use ($filterAgent) {
                $q->where('name', 'like', '%' . $filterAgent . '%');
            });
            $pdcInstallmentsQuery->whereHas('pdc.commitment.agent', function ($q) use ($filterAgent) {
                $q->where('name', 'like', '%' . $filterAgent . '%');
            });
        }

        // Get results based on type filter
        if ($filterType == 'Commitment') {
            $records = $installmentsQuery->orderBy('due_date', 'asc')
                ->paginate(10)
                ->appends($request->query());
        } elseif ($filterType == 'PDC') {
            $records = $pdcInstallmentsQuery->orderBy('due_date', 'asc')
                ->paginate(10)
                ->appends($request->query());
        } else {
            // Union both queries for all records
            $records = $installmentsQuery->unionAll($pdcInstallmentsQuery)
                ->orderBy('due_date', 'asc')
                ->paginate(10)
                ->appends($request->query());
        }

        // Get clients with proper data scoping
        $clients = $this->getAccessibleData(Client::class)->get();
        $notifications = $this->getNotifications();

        // Get departments for filter dropdown with proper data scoping
        $departments = $this->getAccessibleData(\App\Models\Department::class)->active()->get();

        // Use unified view approach
        $views = 'installments_pdcs.index';

        $view = $views[$user->roles->first()->name] ?? 'installments_pdcs.index'; // Fallback to default view

        return view($view, compact('records', 'clients', 'notifications', 'request', 'departments'));
    }
}
