document.addEventListener('DOMContentLoaded', function () {
    // Fade in the alert on page load
    let alertBox = document.querySelectorAll('.custom-alert');


    // Automatically fade out after 5 seconds
    setTimeout(function () {
        alertBox.forEach(function (alert) {
            alert.classList.add('fade-out');
        });
    }, 5000); // 5 seconds

    // Remove the alert from the DOM after the fade-out transition completes
    setTimeout(function () {
        alertBox.forEach(function (alert) {
            alert.remove();
        });
    }, 5500); // 5 seconds + 0.5s for fade-out effect
});


document.addEventListener('click', function (e) {
    const dropdownMenu = document.getElementById('notificationDropdown');
    const dropdownToggle = document.getElementById('navbarDropdownMenuLink2');

    // Check if the click was outside of the dropdown
    if (!dropdownMenu.contains(e.target) && !dropdownToggle.contains(e.target)) {
        const dropdown = new bootstrap.Dropdown(dropdownToggle);
        dropdown.hide();
    }
});



function toggleDropdown(event) {
    event.stopPropagation(); // Prevent click event from propagating to the document
    const dropdown = document.getElementById('messagesDropdown');
    const isOpen = dropdown.classList.contains('show');

    // Toggle the dropdown manually
    if (isOpen) {
        dropdown.classList.remove('show');
    } else {
        dropdown.classList.add('show');
    }
}

// Close the dropdown if clicked outside
document.addEventListener('click', function (e) {
    const dropdownMenu = document.getElementById('messagesDropdown');
    const dropdownToggle = document.getElementById('navbarDropdownMenuLink1');
    const dropdownContainer = document.getElementById('messagesDropdownContainer');

    // If the click was outside the dropdown or toggle button, hide the dropdown
    if (!dropdownContainer.contains(e.target)) {
        dropdownMenu.classList.remove('show');
    }
});

document.addEventListener("DOMContentLoaded", function () {
    function updateStatus() {
        let totalAmount = parseFloat(document.getElementById("total_amount").value) || 0;
        let paidAmount = parseFloat(document.getElementById("paid_amount").value) || 0;
        let dueDate = new Date(document.getElementById("due_date").value);
        let today = new Date();
        let statusField = document.getElementById("status");

        if (paidAmount >= totalAmount && totalAmount > 0) {
            statusField.value = "Complete";
        } else if (today >= dueDate) {
            statusField.value = "Overdue";
        } else {
            statusField.value = "Pending";
        }
    }

    document.getElementById("total_amount").addEventListener("input", updateStatus);
    document.getElementById("paid_amount").addEventListener("input", updateStatus);
    document.getElementById("due_date").addEventListener("change", updateStatus);
});

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("[id^=paid_amount]").forEach(function (paidAmountInput) {
        paidAmountInput.addEventListener("input", function () {
            let modal = this.closest(".modal");
            let totalAmount = parseFloat(modal.querySelector("[id^=total_amount]").value) || 0;
            let paidAmount = parseFloat(this.value) || 0;
            let pendingAmountInput = modal.querySelector("[id^=pending_amount]");
            let statusInput = modal.querySelector("[id^=status]");
            let dueDate = new Date(modal.querySelector("[id^=due_date]").value);
            let today = new Date();

            // Calculate pending amount
            let pendingAmount = totalAmount - paidAmount;
            pendingAmountInput.value = pendingAmount;

            // Change status based on payment
            if (pendingAmount <= 0) {
                statusInput.value = "Complete";
            } else if (pendingAmount > 0) {
                if (dueDate < today) {
                    statusInput.value = "Overdue";
                } else {
                    statusInput.value = "Pending";
                }
            }
        });
    });
});




document.addEventListener("DOMContentLoaded", function () {
    function updatePDCStatus() {
        let commitmentAmount = parseFloat(document.getElementById("commitment_amount").value) || 0;
        let chequeDate = new Date(document.getElementById("cheque_date").value);
        let today = new Date();
        let statusField = document.getElementById("status");

        // If the commitment amount is greater than 0, we proceed with checking dates
        if (commitmentAmount > 0) {
            // If cheque date is in the past, set status as "Overdue"
            if (chequeDate < today) {
                statusField.value = "Overdue";
            }
            // If cheque date is in the future or today, set status as "Pending"
            else {
                statusField.value = "Pending";
            }
        } else {
            // If commitment amount is zero or not entered, set status as "Complete"
            statusField.value = "Complete";
        }
    }

    // Update status whenever there's an input change
    document.getElementById("commitment_amount").addEventListener("input", updatePDCStatus);
    document.getElementById("cheque_date").addEventListener("change", updatePDCStatus);

    // Initial status check when the page loads
    updatePDCStatus();
});

document.addEventListener("DOMContentLoaded", function () {
    const dueDateInput = document.getElementById('cheque_date');
    const commitmentAmountInput = document.getElementById('commitment_amount');
    const statusInput = document.getElementById('status');

    // Function to update the status
    function updateStatus() {
        const dueDate = new Date(dueDateInput.value);
        const commitmentAmount = parseFloat(commitmentAmountInput.value);
        const currentDate = new Date();

        // Determine the status based on commitment amount and due date
        if (commitmentAmount > 0 && currentDate > dueDate) {
            statusInput.value = 'Overdue';
        } else if (commitmentAmount > 0 && currentDate < dueDate) {
            statusInput.value = 'Pending';
        } else {
            statusInput.value = 'Complete';
        }
    }

    // Listen for changes in the due date and commitment amount
    dueDateInput.addEventListener('change', updateStatus);
    commitmentAmountInput.addEventListener('change', updateStatus);

    // Initialize the status when the page loads (in case values are pre-populated)
    updateStatus();
});


$(document).ready(function () {
    $('.sidebar-toggle').on('click', function () {
        // Toggle the sidebar and content
        $(this).toggleClass('active');
        $('#sidebar').toggleClass('shrinked');
        $('.page-content').toggleClass('active');
        $(document).trigger('sidebarChanged');

        // Smoothly toggle the icon and navbar brand visibility
        if ($(this).hasClass('active')) {
            // Show small brand and hide large brand
            $('.navbar-brand .brand-sm').addClass('visible');
            $('.navbar-brand .brand-big').removeClass('visible');
            // Change icon to right arrow when active
            $(this).find('i').removeClass('fa-long-arrow-left').addClass('fa-long-arrow-right');
        } else {
            // Show large brand and hide small brand
            $('.navbar-brand .brand-sm').removeClass('visible');
            $('.navbar-brand .brand-big').addClass('visible');
            // Change icon to left arrow when not active
            $(this).find('i').removeClass('fa-long-arrow-right').addClass('fa-long-arrow-left');
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    // Get all date inputs
    let dateInputs = document.querySelectorAll('input[type="date"]');

    dateInputs.forEach(input => {
        // Add event listener to open date picker on focus or click
        input.addEventListener('focus', function () {
            this.showPicker(); // Opens the native date picker
        });

        input.addEventListener('click', function () {
            this.showPicker(); // Ensures it opens even on a simple click
        });
    });
});


$(document).ready(function () {
    $(".create-followup-btn").click(function () {
        var id = $(this).data("id");

        // Close any open modals
        $(".modal").modal("hide");

        // Wait for the main modal to close, then open the follow-up modal
        setTimeout(function () {
            $("#newfollowupModal" + id).modal("show");
        }, 500);
    });

    // Close follow-up modal and reopen the main modal when needed
    $(".modal").on("hidden.bs.modal", function () {
        var id = $(this).attr("id").replace("newfollowupModal", "");
        if ($("#updateModel" + id).length && !$(".modal.show").length) {
            $("#updateModel" + id).modal("show");
        }
    });
});
