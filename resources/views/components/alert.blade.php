@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if ($success = session()->pull('success'))
            window.showSuccessToast(@json($success));
            @endif

            @if ($error = session()->pull('error'))
            window.showErrorToast(@json($error));
            @endif

            @if ($status = session()->pull('status'))
            window.showSuccessToast(@json(__($status)));
            @endif
        });
    </script>
@endpush
