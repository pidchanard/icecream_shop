const userBtn = document.querySelector('#user-btn');
if (userBtn) {
    userBtn.addEventListener('click', function(){
        const userBox = document.querySelector('.profile-detail');
        if (userBox) userBox.classList.toggle('active');
    })
}

// Nicer confirm dialogs (SweetAlert) for anything with data-confirm,
// replacing the default browser confirm() popup.
document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('click', function (e) {
        e.preventDefault();
        const message = el.getAttribute('data-confirm') || 'Are you sure?';

        // Fall back to native confirm if SweetAlert failed to load.
        if (typeof swal !== 'function') {
            if (confirm(message)) proceed();
            return;
        }

        swal({
            title: 'Are you sure?',
            text: message,
            icon: 'warning',
            buttons: ['Cancel', 'Yes'],
            dangerMode: true,
        }).then(function (confirmed) {
            if (confirmed) proceed();
        });

        function proceed() {
            if (el.tagName === 'A' && el.href) {
                window.location.href = el.href;
            } else if (el.form) {
                // Preserve which submit button was clicked (e.g. name="delete")
                if (el.name) {
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = el.name;
                    hidden.value = el.value || '';
                    el.form.appendChild(hidden);
                }
                el.form.submit();
            }
        }
    });
});