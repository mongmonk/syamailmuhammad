class AudioPlayer {
    constructor() {
        this.players = [];
        this.init();
    }

    init() {
        // Initialize all audio players on the page
        document.addEventListener('DOMContentLoaded', () => {
            this.initializePlayers();
        });
    }

    initializePlayers() {
        const audioElements = document.querySelectorAll('.audio-player');
        
        audioElements.forEach(element => {
            const audioId = element.getAttribute('data-audio-id');
            if (audioId) {
                this.createPlayer(element, audioId);
            }
        });
    }

    createPlayer(container, audioId) {
        // Create player UI
        const playerContainer = document.createElement('div');
        playerContainer.className = 'audio-player-container bg-gray-100 p-4 rounded-lg';
        
        // Create play button
        const playButton = document.createElement('button');
        playButton.className = 'play-button bg-emerald-600 hover:bg-emerald-700 text-white rounded-full w-12 h-12 flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-opacity-50';
        playButton.innerHTML = '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path></svg>';
        
        // Create loading indicator
        const loadingIndicator = document.createElement('div');
        loadingIndicator.className = 'loading-indicator hidden';
        loadingIndicator.innerHTML = '<svg class="animate-spin h-5 w-5 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
        
        // Create progress bar
        const progressContainer = document.createElement('div');
        progressContainer.className = 'progress-container mt-3';
        
        const progressBar = document.createElement('div');
        progressBar.className = 'w-full bg-gray-300 rounded-full h-2';
        
        const progress = document.createElement('div');
        progress.className = 'progress bg-emerald-600 h-2 rounded-full';
        progress.style.width = '0%';
        
        progressBar.appendChild(progress);
        progressContainer.appendChild(progressBar);
        
        // Create time display
        const timeDisplay = document.createElement('div');
        timeDisplay.className = 'time-display flex justify-between text-sm text-gray-600 mt-1';
        timeDisplay.innerHTML = '<span class="current-time">0:00</span><span class="duration">0:00</span>';
        
        // Create audio element (hidden)
        const audioElement = document.createElement('audio');
        audioElement.preload = 'none'; // Lazy loading
        
        // Assemble player
        const controlsContainer = document.createElement('div');
        controlsContainer.className = 'flex items-center space-x-3';
        controlsContainer.appendChild(playButton);
        controlsContainer.appendChild(loadingIndicator);
        
        playerContainer.appendChild(controlsContainer);
        playerContainer.appendChild(progressContainer);
        playerContainer.appendChild(timeDisplay);
        playerContainer.appendChild(audioElement);
        
        // Clear container and add player
        container.innerHTML = '';
        container.appendChild(playerContainer);
        
        // Store player data
        const player = {
            audioId,
            container,
            audioElement,
            playButton,
            loadingIndicator,
            progress,
            currentTimeElement: timeDisplay.querySelector('.current-time'),
            durationElement: timeDisplay.querySelector('.duration'),
            isLoading: false,
            isLoaded: false
        };
        
        this.players.push(player);
        
        // Add event listeners
        this.setupEventListeners(player);
    }

    setupEventListeners(player) {
        // Play button click
        player.playButton.addEventListener('click', () => {
            this.togglePlay(player);
        });
        
        // Audio events
        player.audioElement.addEventListener('loadedmetadata', () => {
            player.durationElement.textContent = this.formatTime(player.audioElement.duration);
            player.isLoaded = true;
            player.isLoading = false;
            player.loadingIndicator.classList.add('hidden');
            player.playButton.classList.remove('hidden');
        });
        
        player.audioElement.addEventListener('timeupdate', () => {
            if (player.audioElement.duration) {
                const progressPercent = (player.audioElement.currentTime / player.audioElement.duration) * 100;
                player.progress.style.width = `${progressPercent}%`;
                player.currentTimeElement.textContent = this.formatTime(player.audioElement.currentTime);
            }
        });
        
        player.audioElement.addEventListener('ended', () => {
            this.resetPlayer(player);
        });
        
        player.audioElement.addEventListener('error', () => {
            this.handleError(player);
        });
        
        // Progress bar click
        player.progress.parentElement.addEventListener('click', (e) => {
            if (player.isLoaded) {
                const rect = e.currentTarget.getBoundingClientRect();
                const pos = (e.clientX - rect.left) / rect.width;
                player.audioElement.currentTime = pos * player.audioElement.duration;
            }
        });
    }

    async togglePlay(player) {
        if (player.isLoading) return;
        
        if (!player.isLoaded) {
            // Load audio on first play (lazy loading)
            await this.loadAudio(player);
            return;
        }
        
        if (player.audioElement.paused) {
            try {
                await player.audioElement.play();
                this.updatePlayButton(player, true);
            } catch (error) {
                console.error('Error playing audio:', error);
                this.handleError(player);
            }
        } else {
            player.audioElement.pause();
            this.updatePlayButton(player, false);
        }
    }

    async loadAudio(player) {
        player.isLoading = true;
        player.playButton.classList.add('hidden');
        player.loadingIndicator.classList.remove('hidden');
        
        try {
            // Fetch audio URL
            const response = await fetch(`/audio/${player.audioId}/url`);
            
            if (!response.ok) {
                throw new Error('Failed to load audio URL');
            }
            
            const data = await response.json();
            
            // Set audio source
            player.audioElement.src = data.url;
            player.audioElement.preload = 'metadata';
            
            // Load audio metadata
            await player.audioElement.load();
            
            // Auto-play after loading
            try {
                await player.audioElement.play();
                this.updatePlayButton(player, true);
            } catch (error) {
                console.error('Auto-play was prevented:', error);
                this.updatePlayButton(player, false);
            }
        } catch (error) {
            console.error('Error loading audio:', error);
            this.handleError(player);
        }
    }

    updatePlayButton(player, isPlaying) {
        if (isPlaying) {
            player.playButton.innerHTML = '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>';
        } else {
            player.playButton.innerHTML = '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path></svg>';
        }
    }

    resetPlayer(player) {
        player.audioElement.currentTime = 0;
        player.audioElement.pause();
        this.updatePlayButton(player, false);
        player.progress.style.width = '0%';
        player.currentTimeElement.textContent = '0:00';
    }

    handleError(player) {
        player.isLoading = false;
        player.playButton.classList.add('hidden');
        player.loadingIndicator.classList.add('hidden');
        
        const errorMessage = document.createElement('div');
        errorMessage.className = 'error-message text-red-600 text-sm mt-2';
        errorMessage.textContent = 'Gagal memuat audio. Silakan coba lagi.';
        
        player.container.appendChild(errorMessage);
        
        // Remove error message after 5 seconds
        setTimeout(() => {
            if (errorMessage.parentElement) {
                errorMessage.parentElement.removeChild(errorMessage);
            }
        }, 5000);
    }

    formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = Math.floor(seconds % 60);
        return `${minutes}:${remainingSeconds < 10 ? '0' : ''}${remainingSeconds}`;
    }
}

// Initialize audio player
new AudioPlayer();