// Rolagem Suave
let hasScrolled = false;
let isScrolling = false;
const sections = document.querySelectorAll('section');
let currentSection = 0;
let isMobile = window.innerWidth <= 768;

// Atualizar estado mobile quando a tela for redimensionada
window.addEventListener('resize', function() {
    isMobile = window.innerWidth <= 768;
    if (isMobile) {
        document.body.style.overflow = 'auto';
        sections.forEach(section => {
            section.style.minHeight = 'auto';
            section.style.height = 'auto';
        });
    } else {
        document.body.style.overflow = hasScrolled ? 'hidden' : 'auto';
        sections.forEach(section => {
            section.style.minHeight = '100vh';
            section.style.height = '100vh';
        });
    }
});

// Navegação por bolinhas
const navDots = document.querySelectorAll('.nav-dot');

function updateNavDots() {
    if (isMobile) return;
    navDots.forEach((dot, index) => {
        dot.classList.toggle('active', index === currentSection);
    });
}

navDots.forEach(dot => {
    dot.addEventListener('click', function() {
        if (isMobile) return;
        const sectionIndex = parseInt(this.dataset.section);
        if (!hasScrolled) {
            hasScrolled = true;
            document.body.style.overflow = 'hidden';
        }
        currentSection = sectionIndex;
        sections[sectionIndex].scrollIntoView({ behavior: 'smooth' });
        updateNavDots();
        updateFooterVisibility();
    });
});

// Atualizar footer
function updateFooterVisibility() {
    if (isMobile) return;
    const footer = document.querySelector('.footer');
    footer.classList.toggle('show', currentSection === 1);
}

window.addEventListener('wheel', function(e) {
    if (isMobile) return;
    if (isScrolling) return;
    
    if (!hasScrolled) {
        hasScrolled = true;
        document.body.style.overflow = 'hidden';
    }

    isScrolling = true;
    setTimeout(() => isScrolling = false, 1000);

    if (e.deltaY > 0 && currentSection < sections.length - 1) {
        currentSection++;
    } else if (e.deltaY < 0 && currentSection > 0) {
        currentSection--;
    }

    sections[currentSection].scrollIntoView({
        behavior: 'smooth'
    });
    updateNavDots();
    updateFooterVisibility();
});

// Scroll indicator
document.querySelector('.scroll-indicator').addEventListener('click', function(e) {
    if (isMobile) return;
    e.preventDefault();
    hasScrolled = true;
    document.body.style.overflow = 'hidden';
    currentSection = 1;
    sections[currentSection].scrollIntoView({
        behavior: 'smooth'
    });
    updateNavDots();
    updateFooterVisibility();
});

// Player de Rádio
document.addEventListener('DOMContentLoaded', function() {
    const musicBtn = document.getElementById('music-toggle');
    const musicIcon = musicBtn.querySelector('.material-icons-round');
    const audioPlayer = document.getElementById('audio-player');
    const channels = document.querySelectorAll('.channel-option');
    const radioChannels = document.querySelector('.radio-channels');
    const musicPlayer = document.querySelector('.music-player');
    const volumeSlider = document.getElementById('volume-slider');
    const volumeIcon = document.querySelector('.volume-icon');
    let isPlaying = false;
    let currentChannel = channels[0];
    let isLoading = false;
    let lastVolume = 1;
    let menuTimeout;

    // Controle de Play/Pause
    musicBtn.addEventListener('click', function() {
        if (isPlaying) {
            pauseChannel();
        } else if (currentChannel) {
            playChannel(currentChannel);
        }
    });

    // Controle de Volume
    volumeSlider.addEventListener('input', function() {
        const volume = this.value / 100;
        audioPlayer.volume = volume;
        updateVolumeIcon(volume);
    });

    volumeIcon.addEventListener('click', function() {
        if (audioPlayer.volume > 0) {
            lastVolume = audioPlayer.volume;
            audioPlayer.volume = 0;
            volumeSlider.value = 0;
            this.textContent = 'volume_off';
        } else {
            audioPlayer.volume = lastVolume;
            volumeSlider.value = lastVolume * 100;
            updateVolumeIcon(lastVolume);
        }
    });

    function updateVolumeIcon(volume) {
        if (volume === 0) volumeIcon.textContent = 'volume_off';
        else if (volume < 0.5) volumeIcon.textContent = 'volume_down';
        else volumeIcon.textContent = 'volume_up';
    }

    function playChannel(channel) {
        if (isLoading) return;
        isLoading = true;
        musicBtn.classList.add('loading');
        
        const url = channel.dataset.url;
        const channelName = channel.querySelector('span:last-child').textContent;
        document.querySelector('.radio-title').textContent = channelName;
        
        audioPlayer.src = url;
        audioPlayer.play()
            .then(() => {
                isPlaying = true;
                isLoading = false;
                musicBtn.classList.remove('loading');
                musicBtn.classList.add('playing');
                currentChannel.classList.remove('active');
                channel.classList.add('active');
                currentChannel = channel;
                musicIcon.textContent = 'pause';
                radioChannels.classList.remove('show');
            })
            .catch(error => {
                console.error('Erro ao tocar rádio:', error);
                isPlaying = false;
                isLoading = false;
                musicBtn.classList.remove('loading');
                musicBtn.classList.remove('playing');
                musicIcon.textContent = 'play_arrow';
            });
    }

    function pauseChannel() {
        if (isLoading) return;
        audioPlayer.pause();
        isPlaying = false;
        musicBtn.classList.remove('playing');
        musicIcon.textContent = 'play_arrow';
    }

    channels.forEach(channel => {
        channel.addEventListener('click', () => {
            if (currentChannel === channel && isPlaying) {
                pauseChannel();
                radioChannels.classList.add('show');
            } else {
                playChannel(channel);
            }
        });
    });

    audioPlayer.addEventListener('error', () => {
        musicBtn.classList.remove('playing');
        isPlaying = false;
        radioChannels.classList.add('show');
    });

    // Controle do Menu
    document.addEventListener('click', function(event) {
        if (!musicPlayer.contains(event.target)) {
            radioChannels.classList.remove('show');
        }
    });

    musicBtn.addEventListener('mouseenter', function() {
        radioChannels.classList.add('show');
    });

    musicBtn.addEventListener('mouseleave', function() {
        menuTimeout = setTimeout(() => {
            if (!radioChannels.matches(':hover')) {
                radioChannels.classList.remove('show');
            }
        }, 300);
    });

    radioChannels.addEventListener('mouseenter', function() {
        clearTimeout(menuTimeout);
    });

    radioChannels.addEventListener('mouseleave', function() {
        menuTimeout = setTimeout(() => {
            radioChannels.classList.remove('show');
        }, 300);
    });
}); 