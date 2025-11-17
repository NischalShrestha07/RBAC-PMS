<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page ?? '' }} - PMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <link rel="icon" href="{{asset('admincss/img/pms.ico')}}" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        #sidebarMenu.collapsed .profile-wrapper img {
            width: 50px !important;
            height: 50px !important;
        }

        #sidebarMenu.collapsed .profile-wrapper a {
            width: 10px !important;
            height: 10px !important;
        }

        #sidebarMenu.collapsed .profile-wrapper a i {
            font-size: 6px !important;
        }

        #sidebarMenu.collapsed .sidebar-title,
        #sidebarMenu.collapsed .nav-link span,
        #sidebarMenu.collapsed .user-info {
            display: none;
        }

        #sidebarMenu.collapsed .nav-link {
            text-align: center;
            font-size: 2rem;
        }

        #sidebarMenu.collapsed .nav-link i {
            margin: 0 !important;
        }

        #sidebarMenu a.active {
            color: blue !important;
        }

        #sidebarMenu.collapsed .company-name {
            display: none !important;
        }

        #sidebarMenu.collapsed .company-short {
            display: inline !important;
            text-decoration: none
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <nav id="sidebarMenu"
            class="d-flex flex-column justify-content-between gap-3 text-black sidebar shadow-sm bg-white z-3">
            <div class="position-sticky">
                <div class="p-3 mb-2 border-bottom border-secondary d-flex">
                    <div class="profile-wrapper d-flex justify-content-center align-items-center w-100">
                        <div class="position-relative">
                            <img src="{{ asset('uploads/images/'.Auth::user()->image) }}"
                                class="rounded-circle border border-2 border-primary" width="80" height="80"
                                alt="Admin User">
                            <a href="{{route('electronics.profile.update')}}"
                                class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 d-flex justify-content-center align-items-center"
                                style="font-size: 16px; opacity: 0.7; transition: opacity 0.3s ease, transform 0.3s ease; width: 32px; height: 32px; cursor: pointer;"
                                onmouseover="this.style.opacity='1'; this.style.transform='scale(1.2)'"
                                onmouseout="this.style.opacity='0.7'; this.style.transform='scale(1)'">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-primary toggle-btn" id="toggleSidebar"
                        style="position: absolute;top: 15px;right: -50px;"><i
                            class="bi bi-chevron-double-right"></i></button>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item px-1 py-1">
                        <a class="nav-link text-nowrap text-black {{ request()->routeIs('electronics.dashboard') ? 'active' : '' }}"
                            href="{{route('electronics.dashboard')}} ">
                            <i class="bi bi-speedometer2 me-2"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item px-1 py-1">
                        <a class="nav-link text-nowrap text-black {{ request()->routeIs('electronics.commitments.create') ? 'active' : '' }}"
                            href="{{route('electronics.commitments.create')}} ">
                            <i class="bi bi-journal-check me-2"></i>
                            <span>Commitments</span>
                        </a>
                    </li>
                    <li class="nav-item px-1 py-1">
                        <a class="nav-link text-nowrap text-black {{ request()->routeIs('electronics.pdc.create') ? 'active' : '' }}"
                            href="{{route('electronics.pdc.create')}} ">
                            <i class="bi bi-receipt-cutoff me-2"></i>
                            <span>PDCs</span>
                        </a>
                    </li>
                    <li class="nav-item px-1 py-1">
                        <a class="nav-link text-nowrap text-black {{ request()->routeIs('electronics.agents.create') ? 'active' : '' }}"
                            href="{{route('electronics.agents.create')}} ">
                            <i class="bi bi-people-fill me-2"></i>
                            <span>Salesman</span>
                        </a>
                    </li>
                    <li class="nav-item px-1 py-1">
                        <a class="nav-link text-nowrap text-black {{ request()->routeIs('electronics.installments_pdcs.index') ? 'active' : '' }}"
                            href="{{route('electronics.installments_pdcs.index')}} ">
                            <i class="bi bi-calendar-check me-2"></i>
                            <span>Installments</span>
                        </a>
                    </li>
                    <li class="nav-item px-1 py-1">
                        <a class="nav-link text-nowrap text-black {{ request()->routeIs('electronics.cheque_locations.index') ? 'active' : '' }}"
                            href="{{route('electronics.cheque_locations.index')}} ">
                            <i class="bi bi-geo-alt-fill me-2"></i>
                            <span>Cheque Location</span>
                        </a>
                    </li>
                    <li class="nav-item px-1 py-1">
                        <a class="nav-link text-nowrap text-black {{ request()->routeIs('electronics.clients.index') ? 'active' : '' }}"
                            href="{{route('electronics.clients.index')}} ">
                            <i class="bi bi-person-lines-fill me-2"></i>
                            <span>Clients/Parties</span>
                        </a>
                    </li>
                </ul>
            </div>
            <footer class="p-2 text-center text-muted border-top small company-name">
                <small>© 2025 PMS.</small>
                <div>By
                    <a href="https://itservicenepal.com?ref=pallavipms" target="_blank"
                        class="text-decoration-none fw-semibold company-name">
                        IT Service Nepal
                    </a>
                </div>
            </footer>
            <footer class="p-2 company-short d-none text-center text-muted border-top small company-name">
                <small>© 2025.</small>
                <div>By
                    <a href="https://itservicenepal.com?ref=pallavipms" target="_blank"
                        class="text-decoration-none fw-bold">
                        ITSN
                    </a>
                </div>
            </footer>
        </nav>

        <main class="px-4 pb-4 w-100" style="height:100dvh; overflow:auto;">
            <div class="d-flex justify-content-end align-items-center border-bottom position-relative py-3 mb-3">
                <div class="position-absolute top-50 start-50 translate-middle z-2 m-0">
                    <img src="{{asset('admincss/img/pms.svg')}}" alt="" style="max-width: 100%;">
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="position-relative me-3">
                        <a href="{{route('electronics.parts.notifications')}}"
                            class="btn btn-outline-secondary position-relative">
                            <i class="bi bi-bell fs-5"></i>
                            <span
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $notifications['pdcCount'] + $notifications['commitmentCount'] }}
                                <span class="visually-hidden">unread messages</span>
                            </span>
                        </a>
                    </div>
                    <div class="dropdown me-3">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle"
                            id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ asset('uploads/images/'.Auth::user()->image) }}" class="rounded-circle me-2"
                                width="40" height="40" alt="{{Auth::user()->name}}">
                            <span class="d-none d-sm-inline text-black">{{Auth::user()->name}}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="{{route('electronics.profile.update')}}"><i
                                        class="bi bi-person me-2"></i> Profile</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="{{ route('logout') }}"><i
                                        class="bi bi-box-arrow-right me-2"></i> Sign
                                    out</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('sidebarMenu');
        const toggleBtn = document.getElementById('toggleSidebar');

        // Restore state
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            sidebar.classList.add('collapsed');
            toggleBtn.innerHTML = '<i class="bi bi-chevron-double-right"></i>';
        } else {
            sidebar.classList.remove('collapsed');
            toggleBtn.innerHTML = '<i class="bi bi-chevron-double-left"></i>';
        }

        // Toggle and save
        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
            toggleBtn.innerHTML = isCollapsed
                ? '<i class="bi bi-chevron-double-right"></i>'
                : '<i class="bi bi-chevron-double-left"></i>';
        });
    });
    </script>

    {{-- recent update modal --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
      const modal = document.getElementById('detailsModal');
      modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const isRecentUpdate = button.classList.contains('btn'); // From Recent Updates table
        const isSummary = button.classList.contains('agent-history'); // From Summary tables

        const modalHeader = document.getElementById('modalHeader');
        const modalTitle = document.getElementById('detailsModalLabel');
        const modalSubtitle = document.getElementById('modalSubtitle');
        const installmentsContainer = document.getElementById('installmentsContainer');

        // Clear previous content
        installmentsContainer.innerHTML = '';

        if (isRecentUpdate) {
            // Handle Recent Updates table (existing logic)
            const type = button.getAttribute('data-type');
            const clientName = button.getAttribute('data-client-name');
            const installments = JSON.parse(button.getAttribute('data-installments'));

            // Set modal header and title
            modalHeader.className = 'modal-header ' + (type === 'commitment' ? 'bg-light' : 'bg-light') + ' text-black';
            modalTitle.textContent = (type === 'commitment' ? 'Installments' : 'PDC Installments') + ' for ' + clientName;
            modalSubtitle.textContent = type === 'commitment' ? '📑 Installment Details' : '📑 PDC Details';

            // Generate installment cards
            installments.forEach(installment => {
                const dueDate = new Date(installment.due_date);
                const now = new Date();
                const daysLeft = Math.round((dueDate - now) / (1000 * 60 * 60 * 24));

                const statusClass =
                    installment.status === 'Complete' ? 'bg-success' :
                    installment.status === 'Pending' ? 'bg-warning' : 'bg-danger';

                const statusText =
                    installment.status === 'Complete' ? '✅ Complete' :
                    daysLeft < 0 ? `⏳ Overdue by ${Math.abs(daysLeft)} days` :
                    `📅 ${daysLeft} days left`;

                const card = document.createElement('div');
                card.className = '';
                card.innerHTML = `
                    <div class="card border-0 shadow-sm p-3 rounded h-100">
                        ${type === 'pdc' ? `<h6><strong>Cheque No:</strong> ${installment.chequeno}</h6>` : ''}
                        <h6><strong>Amount:</strong> Rs ${Number(installment.amount).toFixed(2)}</h6>
                        <p><strong>Due Date:</strong> ${installment.due_date}</p>
                        <p><strong>Status:</strong>
                            <span style="color: white" class="badge ${statusClass}">${installment.status}</span>
                        </p>
                        <div><span class="${installment.status === 'Complete' ? 'text-success' : daysLeft < 0 ? 'text-danger' : 'text-success'} fw-bold">${statusText}</span></div>
                    </div>
                `;
                installmentsContainer.appendChild(card);
            });
        } else if (isSummary) {
            // Handle Salesman and Party Summary tables
            const entityType = button.getAttribute('data-entity-type'); // 'salesman' or 'party'
            const type = button.getAttribute('data-type'); // 'commitment' or 'pdc'
            const agentName = button.getAttribute('data-agent-name');
            const department = button.getAttribute('data-department') || '';
            const items = JSON.parse(button.getAttribute('data-items'));

            // Set modal header and title
            modalHeader.className = 'modal-header ' + (type === 'commitment' ? 'bg-light' : 'bg-light') + ' text-black';
            modalTitle.textContent = (type === 'commitment' ? 'Commitment' : 'PDC') + ' Details for ' + agentName + (entityType === 'salesman' && department ? ' (' + department + ')' : '');
            modalSubtitle.textContent = type === 'commitment' ? '📑 Commitment Details' : '📑 PDC Details';

            // Generate item cards
            items.forEach(item => {
                const dueDate = new Date(item.due_date);
                const now = new Date();
                const daysLeft = Math.round((dueDate - now) / (1000 * 60 * 60 * 24));

                const statusClass =
                    item.status === 'Complete' ? 'bg-success' :
                    item.status === 'Pending' ? 'bg-warning' : 'bg-danger';

                const statusText =
                    item.status === 'Complete' ? '✅ Complete' :
                    daysLeft < 0 ? `⏳ Overdue by ${Math.abs(daysLeft)} days` :
                    `📅 ${daysLeft} days left`;

                const card = document.createElement('div');
                card.className = '';
                card.innerHTML = `
                    <div class="card border-0 shadow-sm p-3 rounded h-100">
                        ${type === 'pdc' ? `<h6><strong>Cheque No:</strong> ${item.chequeno || 'N/A'}</h6>` : ''}
                        <h6><strong>Amount:</strong> Rs ${Number(item.amount || item.total_amount).toFixed(2)}</h6>
                        <p><strong>Due Date:</strong> ${item.due_date}</p>
                        <p><strong>Status:</strong>
                            <span style="color: white" class="badge ${statusClass}">${item.status}</span>
                        </p>
                        <div><span class="${item.status === 'Complete' ? 'text-success' : daysLeft < 0 ? 'text-danger' : 'text-success'} fw-bold">${statusText}</span></div>
                        ${entityType === 'salesman' ? `<p><strong>Party:</strong> ${item.client?.name || 'N/A'}</p>` : `<p><strong>Salesman:</strong> ${item.agent?.name || 'N/A'}</p>`}
                    </div>
                `;
                installmentsContainer.appendChild(card);
            });
        }
      });
     });
    </script>

    {{-- Toogle in Dashboard --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.agent-history').forEach(item => {
            item.addEventListener('click', function () {
                let agentName = this.getAttribute('data-agent-name');
                let commitments = JSON.parse(this.getAttribute('data-commitments'));
                let pdcs = JSON.parse(this.getAttribute('data-pdcs'));
                let type = this.getAttribute('data-type');

                // Set modal title based on type
                document.getElementById('historyModalLabel').innerText =
                    type === 'salesman' ? 'Salesman Active Commitment & PDC' : 'Party Active Commitment & PDC';
                document.getElementById('agentName').innerText = agentName;

                // Sort commitments by date (descending)
                commitments.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

                // Populate commitments
                let commitmentTable = document.getElementById('commitmentData');
                commitmentTable.innerHTML = "";
                let totalCommitmentAmount = 0;
                commitments.forEach((data, index) => {
                    let formattedDate = new Date(data.created_at).toISOString().split('T')[0];
                    commitmentTable.innerHTML += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${data.name}</td>
                            <td>${formattedDate}</td>
                            <td>${data.status || 'N/A'}</td>
                            <td>${parseFloat(data.total_amount).toFixed(2)}</td>
                        </tr>
                    `;
                    totalCommitmentAmount += parseFloat(data.total_amount) || 0;
                });

                // Show total commitment amount
                commitmentTable.innerHTML += `
                    <tr>
                        <td colspan="4" class="text-end"><strong>Total Commitment Amount</strong></td>
                        <td><strong>${totalCommitmentAmount.toFixed(2)}</strong></td>
                    </tr>
                `;

                // Sort PDCs by date (descending)
                pdcs.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

                // Populate PDCs
                let pdcTable = document.getElementById('pdcData');
                pdcTable.innerHTML = "";
                let totalPDCAmount = 0;
                pdcs.forEach((data, index) => {
                    let formattedDate = new Date(data.created_at).toISOString().split('T')[0];
                    pdcTable.innerHTML += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${data.name}</td>
                            <td>${formattedDate}</td>
                            <td>${data.status || 'N/A'}</td>
                            <td>${parseFloat(data.commitment_amount).toFixed(2)}</td>
                        </tr>
                    `;
                    totalPDCAmount += parseFloat(data.commitment_amount) || 0;
                });

                // Show total PDC amount
                pdcTable.innerHTML += `
                    <tr>
                        <td colspan="4" class="text-end"><strong>Total PDC Amount</strong></td>
                        <td><strong>${totalPDCAmount.toFixed(2)}</strong></td>
                    </tr>
                `;
            });
        });
     });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{asset('admincss/js/script.js')}}"></script>

    {{-- Search Client Dropdown --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".searchable-dropdown").forEach(function (dropdownWrapper) {
            const searchInput = dropdownWrapper.querySelector(".dropdown-search");
            const dropdownItems = dropdownWrapper.querySelectorAll(".dropdown-item-option");
            const hiddenInput = dropdownWrapper.querySelector(".dropdown-hidden-input");
            const dropdownToggle = dropdownWrapper.querySelector(".dropdown-toggle-btn");

            if (!searchInput || !dropdownItems.length) return;

            searchInput.addEventListener("input", function () {
            const filter = this.value.toLowerCase();
            dropdownItems.forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(filter) ? "" : "none";
            });
            });

            dropdownItems.forEach(item => {
            item.addEventListener("click", function (e) {
            e.preventDefault();
            const value = this.dataset.value;
            hiddenInput.value = value;
            dropdownToggle.textContent = value;
            });
            });
            });
            });
    </script>
</body>

</html>