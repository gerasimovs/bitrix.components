document.addEventListener('DOMContentLoaded', function() {
    let dropdownButtons = document.querySelectorAll('[data-toggle="dropdown"]');

    if (dropdownButtons.length > 0) {
        dropmenuHide = function (event) {
            if (event.target && event.target.closest('.catalog-sorting__dropdown')) {
                return;
            }

            document.querySelectorAll('[data-toggle="dropdown"]').forEach(function (button) {
                let target = document.querySelector(button.dataset.target);
                target.classList.remove('show');
                button.classList.remove('active');
            });

            document.body.removeEventListener('click', dropmenuHide)
        };

        dropdownButtons.forEach(function (button) {
            button.addEventListener('click', function (event) {
                let target = document.querySelector(this.dataset.target);
                if (target.classList.contains('show')) {
                    target.classList.remove('show');
                    this.classList.remove('active');
                    document.body.removeEventListener('click', dropmenuHide)
                } else {
                    target.classList.add('show');
                    this.classList.add('active');
                    document.body.addEventListener('click', dropmenuHide);
                }
            });
        });
    }
});