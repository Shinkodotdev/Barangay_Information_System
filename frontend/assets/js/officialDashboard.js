const slidePositions = {};

function nextSlide(id) {
    const carousel = document.getElementById(id);
    if (!carousel) return;
    const container = carousel.children[0];
    slidePositions[id] = (slidePositions[id] || 0) + 1;
    if (slidePositions[id] >= container.children.length) slidePositions[id] = 0;
    container.style.transform = `translateX(-${container.children[0].offsetWidth * slidePositions[id]}px)`;
}
setInterval(() => nextSlide('announcementsCarousel'), 5000);
setInterval(() => nextSlide('eventsCarousel'), 7000);

function openRequestModal() {
    const modal = document.getElementById('requestModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex', 'items-center', 'justify-center');
}

function closeRequestModal() {
    const modal = document.getElementById('requestModal');
    modal.classList.remove('flex', 'items-center', 'justify-center');
    modal.classList.add('hidden');
}

// Handle form submit
document.getElementById('requestForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('../../../backend/requests/add_request.php', {
        method: 'POST',
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            alert(data.message || 'Request submitted successfully!');
            closeRequestModal();
            location.reload(); // refresh to show in "Recent Requests"
        })
        .catch(err => {
            console.error(err);
            alert('Something went wrong.');
        });
});