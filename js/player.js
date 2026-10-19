document.addEventListener('DOMContentLoaded', () => {
  const audios = document.querySelectorAll('audio[data-free-seconds]');

  audios.forEach((audio) => {
    const freeSeconds = parseInt(audio.dataset.freeSeconds || '60', 10);
    const isPremium = audio.dataset.isPremium === '1';
    const banner = audio.parentElement.querySelector('.upgrade-banner');
    if (isPremium) return;

    let locked = false;

    const showBanner = () => {
      if (banner) {
        banner.classList.add('visible');
      }
    };

    const enforceLimit = () => {
      if (audio.currentTime >= freeSeconds) {
        audio.pause();
        audio.currentTime = freeSeconds - 0.2;
        locked = true;
        showBanner();
      }
    };

    audio.addEventListener('timeupdate', enforceLimit);
    audio.addEventListener('play', () => {
      if (locked) {
        audio.pause();
        showBanner();
      }
    });
  });
});
