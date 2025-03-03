async function loadPhone() {
    try {
        const container = document.getElementById('phoneContainer');
        const display = document.getElementById('phoneDisplay');

        if (container.dataset.loaded) return;

        const response = await BX.ajax.runComponentAction('jubiks:resume', 'getPhoneNumber', {
            mode: 'class',
            data: {
                id: BX.message('RESUME_ID')
            }
        });

        if (response.data) {
            const encrypted = await response.data;

            // Расшифровка номера (пример с Base64)
            const decodedPhone = atob(encrypted);
            display.textContent = formatPhone(decodedPhone);
            display.href = 'tel:+' + decodedPhone;

            // Обновляем состояние
            container.dataset.loaded = true;
            display.classList.add('phone-visible');
        } else {
            throw new Error('Ошибка загрузки');
        }
    } catch (error) {
        console.error('Ошибка:', error);
        display.textContent = 'Телефон недоступен';
    }
}

function formatPhone(number) {
    return number.replace(/(\d{1})(\d{3})(\d{3})(\d{2})(\d{2})/, '+$1 ($2) $3-$4-$5');
}

function downloadPDF() {
    const content = document.documentElement.outerHTML;
    const parser = new DOMParser();
    const doc = parser.parseFromString(content, 'text/html');

    doc.querySelectorAll('script, link, meta, noscript').forEach(el => el.remove());

    const cleanedContent = doc.documentElement.outerHTML;
    const encodedContent = encodeURIComponent(cleanedContent);

    fetch('/local/ajax/pdf-generator.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'htmlContent=' + encodedContent
    })
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.blob();
        })
        .then(blob => {
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'document.pdf';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        })
        .catch(error => console.error('Error:', error));
}

// Альтернативная защита через задержку
window.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        loadPhone();
    }, 3000);

    $('.certificates > .certificates-list').slick({
        dots: false,
        speed: 300,
        variableWidth: true,
        slidesToShow: 3,
        slidesToScroll: 1,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3,
                    arrows: false,
                    dots: true
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2,
                    arrows: false,
                    dots: true
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false,
                    dots: true
                }
            }
        ]
    });
});