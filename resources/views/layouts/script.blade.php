<!-- End Page -->

<!-- JQuery min js -->
<script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>

<!-- Bootstrap Bundle js -->
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<!-- Ionicons js -->
<script src="{{ asset('assets/plugins/ionicons/ionicons.js') }}"></script>

<!-- Moment js -->
<script src="{{ asset('assets/plugins/moment/moment.js') }}"></script>

<!-- P-scroll js -->
<script src="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('assets/plugins/perfect-scrollbar/p-scroll.js') }}"></script>

<!-- eva-icons js -->
<script src="{{ asset('assets/js/eva-icons.min.js') }}"></script>

<!-- Rating js-->
<script src="{{ asset('assets/plugins/rating/jquery.rating-stars.js') }}"></script>
<script src="{{ asset('assets/plugins/rating/jquery.barrating.js') }}"></script>

<!-- custom js -->
<script src="{{ asset('assets/js/custom.js') }}"></script>


<!--Internal  Chart.bundle js -->
<script src="{{ asset('assets/plugins/chart.js/Chart.bundle.min.js') }}"></script>

<!-- Ionicons js -->
<script src="{{ asset('assets/plugins/ionicons/ionicons.js') }}"></script>

<!-- Moment js -->
<script src="{{ asset('assets/plugins/moment/moment.js') }}"></script>

<!--Internal Sparkline js -->
<script src="{{ asset('assets/plugins/jquery-sparkline/jquery.sparkline.min.js') }}"></script>

<!-- Moment js -->
<script src="{{ asset('assets/plugins/raphael/raphael.min.js') }}"></script>

<!--Internal Apexchart js-->
<script src="{{ asset('assets/js/apexcharts.js') }}"></script>

<!--Internal  Perfect-scrollbar js -->
<script src="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('assets/plugins/perfect-scrollbar/p-scroll.js') }}"></script>

<!-- Eva-icons js -->
<script src="{{ asset('assets/js/eva-icons.min.js') }}"></script>

<!-- right-sidebar js -->
<script src="{{ asset('assets/plugins/sidebar/sidebar.js') }}"></script>
<script src="{{ asset('assets/plugins/sidebar/sidebar-custom.js') }}"></script>

<!-- Sticky js -->
<script src="{{ asset('assets/js/sticky.js') }}"></script>
<script src="{{ asset('assets/js/modal-popup.js') }}"></script>

<!-- Left-menu js-->
<script src="{{ asset('assets/plugins/side-menu/sidemenu.js') }}"></script>

<!-- Internal Map -->
<script src="{{ asset('assets/plugins/jqvmap/jquery.vmap.min.js') }}"></script>
<script src="{{ asset('assets/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>

<!--Internal  index js -->
<script src="{{ asset('assets/js/index.js') }}"></script>

<!-- Apex chart js-->
<script src="{{ asset('assets/js/apexcharts.js') }}"></script>
<script src="{{ asset('assets/js/jquery.vmap.sampledata.js') }}"></script>

<!--Internal  Datepicker js -->
<script src="{{ asset('assets/plugins/jquery-ui/ui/widgets/datepicker.js') }}"></script>
<!--Internal  Flot js -->
<script src="{{ asset('assets/plugins/jquery.flot/jquery.flot.js') }}"></script>
<script src="{{ asset('assets/plugins/jquery.flot/jquery.flot.pie.js') }}"></script>
<script src="{{ asset('assets/plugins/jquery.flot/jquery.flot.resize.js') }}"></script>
<!--Internal Select2 js-->
<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
<!-- P-scroll js -->
<script src="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('assets/plugins/perfect-scrollbar/p-scroll.js') }}"></script>
<!--eva-icons js -->
<script src="{{ asset('assets/js/eva-icons.min.js') }}"></script>
<!--Internal Chart flot js -->
<script src="{{ asset('assets/js/chart.flot.js') }}"></script>

<script>
    $(document).ready(function () {
        $('#email').on('keyup', function () {
            var emailInput = $(this);
            var email = emailInput.val();
            var isValidEmail = validateEmail(email);

            if (isValidEmail) {
                emailInput.removeClass('is-invalid');
                $('#email-error').text('');

            } else {
                emailInput.addClass('is-invalid');
                $('#email-error').text('Please enter a valid email address.');
            }
        });

        function validateEmail(email) {
            // Regular expression for email format validation
            var emailRegex = /^[\w-\.]+@((?!.*\.com.*\.com)[\w-]+\.)+[\w-]+$/;
            return emailRegex.test(email);
        }

        $('#phone').on('keydown', function (e) {
            var input = e.target;
            var phoneNumber = input.value;
            var formattedNumber = formatPhoneNumber(phoneNumber);
            // Check if the format is complete and the pressed key is not a deletion key
            if (formattedNumber.length >= 14 && e.key.length === 1) {
                e.preventDefault(); // Prevent typing additional characters
            }
        });

        $('#phone').on('input', function (e) {
            var input = e.target;
            var phoneNumber = input.value;
            var formattedNumber = formatPhoneNumber(phoneNumber);
            input.value = formattedNumber;
        });
    });

    function formatPhoneNumber(phoneNumber) {
        // Remove all non-digit characters
        var cleaned = phoneNumber.replace(/\D/g, '');
        // Apply the desired phone number format
        var pattern = /(\d{3})(\d{3})(\d{4})/;
        var formattedNumber = cleaned.replace(pattern, '$1-$2-$3');
        return formattedNumber;
    }


    $(document).ready(function () {
        // toastr.options.timeOut = 10000;
        toastr.options = {
            "closeButton": true,
            "progressBar": true
        }
        @if (Session::has('error'))
        toastr.error('{{ Session::get('error') }}');
        @elseif (Session::has('success'))
        toastr.success('{{ Session::get('success') }}');
        @endif

    });
</script>
