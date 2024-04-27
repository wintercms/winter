((Snowboard, $) => {
    /**
     * Media finder form widget.
     *
     * Allows for the selection of files within the Winter media manager in a form.
     *
     * @author Alexey Bobkov, Samuel Georges (original implementation)
     * @author Ben Thomson <git@alfreido.com> (Snowboard implementation)
     * @copyright 2023 Winter CMS.
     */
    class MediaFinder extends Snowboard.PluginBase {
        construct(element) {
            this.element = element;
            this.config = this.snowboard.dataConfig(this, element);
            this.findButton = element.querySelector('[data-find]');
            this.fileName = element.querySelector('[data-file-name]');
            this.image = element.querySelector('[data-image]');
            this.remove = element.querySelector('[data-remove]');
            this.value = element.querySelector('[data-value]');
            this.item = null;
            this.mediaManager = null;

            this.events = {
                find: (event) => this.find(event),
                remove: (event) => this.clearValue(event),
            };

            this.attachEvents();
            if (['video', 'image'].includes(this.config.get('mode'))) {
                if (this.config.get('imageHeight')) {
                    this.image.style.maxHeight = `${this.config.get('imageHeight')}px`;
                }
                if (this.config.get('imageWidth')) {
                    this.image.style.maxWidth = `${this.config.get('imageWidth')}px`;
                }
            }
        }

        defaults() {
            return {
                mode: 'all',
                isPreview: false,
                imageHeight: null,
                imageWidth: null,
            };
        }

        attachEvents() {
            this.findButton.addEventListener('click', this.events.find);
            this.remove.addEventListener('click', this.events.remove);
        }

        detachEvents() {
            this.findButton.removeEventListener('click', this.events.find);
            this.remove.addEventListener('click', this.events.remove);
        }

        find(event) {
            event.preventDefault();
            event.stopPropagation();

            this.mediaManager = new $.wn.mediaManager.popup({
                alias: 'ocmediamanager',
                cropAndInsertButton: ['image', 'all'].includes(this.config.get('mode')),
                mode: (this.config.get('mode') === 'file') ? 'all' : this.config.get('mode'),
                onInsert: (items) => {
                    if (!items.length) {
                        this.snowboard.flash(
                            `Please select a ${this.config.get('mode')} to insert.`,
                            'error'
                        );
                        return;
                    }

                    if (items.length > 1) {
                        this.snowboard.flash(
                            `Please select a single item.`,
                            'error'
                        );
                        return;
                    }

                    this.item = items[0];
                    this.setValue();
                    if (['video', 'image'].includes(this.config.get('mode'))) {
                        this.updatePreview();
                    } else {
                        this.element.classList.add('is-populated');
                    }

                    this.mediaManager.hide();
                },
            });
        }

        setValue() {
            this.value.value = this.item.path;
            this.fileName.innerText = this.item.path.substring(1);
        }

        clearValue() {
            this.value.value = '';
            this.fileName.innerText = '';
            this.element.classList.remove('is-populated');
        }

        updatePreview() {
            if (this.config.get('mode') === 'video') {
                this.getVideoSnapshot();
            } else if (this.config.get('mode') === 'image') {
                this.getImage();
            }
        }

        getVideoSnapshot() {
            const canvas = document.createElement('canvas');
            const video = document.createElement('video');
            video.width = video.videoWidth;
            video.height = video.videoHeight;

            let image = null;
            const videoSeek = () => {
                // Extract screenshot
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;

                const context = canvas.getContext('2d');
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                image = canvas.toDataURL('image/jpeg');
                this.image.src = image;
                this.element.classList.add('is-populated');

                // Stop loading video
                video.pause();
                video.removeAttribute('src');
                video.load();
                video.removeEventListener('seeked', videoSeek);
                video.removeEventListener('error', videoError);
            };
            const videoError = () => {
                // Stop loading video
                video.pause();
                video.removeAttribute('src');
                video.load();
                video.removeEventListener('seeked', videoSeek);
                video.removeEventListener('error', videoError);
            };

            video.src = this.item.publicUrl;
            video.currentTime = 10;

            video.addEventListener('seeked', videoSeek);
        }

        getImage() {
            this.image.src = this.item.publicUrl;
            this.element.classList.add('is-populated');
        }

        destruct() {
            this.detachEvents();
            this.findButton = null;
            this.fileName = null;
            this.value = null;
            this.image = null;
            this.remove = null;
            this.element = null;
        }
    }

    Snowboard.addPlugin('backend.formwidgets.mediafinder', MediaFinder);
    Snowboard['backend.ui.widgetHandler']().register('mediafinder', 'backend.formwidgets.mediafinder');
})(window.Snowboard, window.jQuery);
