window.countdownIntervals = window.countdownIntervals || {};

function initializeCountdown(elementId, endDate) {
  const el = document.getElementById(elementId);
  if (!el) return;

  // Prefer data-date on the DOM element if set
  if (el.dataset && el.dataset.date) {
    endDate = el.dataset.date;
  }

  if (!endDate) {
    // Default fallback: end of today
    const tonight = new Date();
    tonight.setHours(23, 59, 59, 999);
    endDate = tonight.getTime();
  }

  // Clear existing interval if already running for this element
  if (window.countdownIntervals[elementId]) {
    clearInterval(window.countdownIntervals[elementId]);
  }

  const second = 1000,
        minute = second * 60,
        hour = minute * 60,
        day = hour * 24;

  const countDown = new Date(endDate).getTime();

  function update() {
      const now = new Date().getTime(),
            distance = countDown - now;

      const daysElement = el.querySelector('.days');
      const hoursElement = el.querySelector('.hours');
      const minutesElement = el.querySelector('.minutes');
      const secondsElement = el.querySelector('.seconds');

      if (daysElement && hoursElement && minutesElement && secondsElement) {
        if (distance > 0) {
            daysElement.innerText = String(Math.floor(distance / day)).padStart(2, '0');
            hoursElement.innerText = String(Math.floor((distance % day) / hour)).padStart(2, '0');
            minutesElement.innerText = String(Math.floor((distance % hour) / minute)).padStart(2, '0');
            secondsElement.innerText = String(Math.floor((distance % minute) / second)).padStart(2, '0');
        } else {
            daysElement.innerText = '00';
            hoursElement.innerText = '00';
            minutesElement.innerText = '00';
            secondsElement.innerText = '00';
        }
      }

      if (distance < 0) {
          clearInterval(window.countdownIntervals[elementId]);
      }
  }

  update();
  window.countdownIntervals[elementId] = setInterval(update, 1000);
}

// Auto-initialize any countdown elements with data-date attribute on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.countdown[data-date]').forEach(function(el) {
        if (el.id) {
            initializeCountdown(el.id, el.dataset.date);
        }
    });
});
