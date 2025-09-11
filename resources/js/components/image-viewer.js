function imageViewer(imageUrl) {
    return {
        imageUrl,
        showModal: false,
        zoom: 1,
        minZoom: 0.5,
        maxZoom: 3,
        offsetX: 0,
        offsetY: 0,
        dragging: false,
        startX: 0,
        startY: 0,
        imgW: 1,
        imgH: 1,
        containerW: 1,
        containerH: 1,
        startOffsetX: 0,
        startOffsetY: 0,
        
        get imageStyle() {
            return `transform: scale(${this.zoom}) translate(${this.offsetX/this.zoom}px, ${this.offsetY/this.zoom}px); 
                    transition: ${this.dragging ? 'none' : 'transform 0.15s'}; 
                    cursor: ${this.zoom > 1 ? 'grab' : 'default'};
                    ${this.dragging && this.zoom > 1 ? 'cursor: grabbing;' : ''}`;
        },
        
        clamp(val, min, max) { 
            return Math.max(min, Math.min(max, val)); 
        },
        
        openModal() {
            this.showModal = true;
            this.zoomReset();
            this.$nextTick(() => {
                this.initContainer();
                // Focus trap - focus the close button
                this.$el.querySelector('button[aria-label="Cerrar modal"]').focus();
            });
        },
        
        closeModal() {
            this.showModal = false;
            this.zoomReset();
        },
        
        initContainer() {
            if (this.$refs.imgContainer) {
                this.containerW = this.$refs.imgContainer.offsetWidth;
                this.containerH = this.$refs.imgContainer.offsetHeight;
                
                // Add resize observer for responsive behavior
                if (window.ResizeObserver) {
                    const observer = new ResizeObserver(() => {
                        this.containerW = this.$refs.imgContainer.offsetWidth;
                        this.containerH = this.$refs.imgContainer.offsetHeight;
                        this.adjustOffsets();
                    });
                    observer.observe(this.$refs.imgContainer);
                }
            }
        },
        
        onImgLoad(event) {
            this.imgW = event.target.naturalWidth;
            this.imgH = event.target.naturalHeight;
            this.$nextTick(() => this.zoomToFit());
        },
        
        startDrag(e) {
            if (this.zoom === 1) return;
            this.dragging = true;
            const clientX = e.type.startsWith('touch') ? e.touches[0].clientX : e.clientX;
            const clientY = e.type.startsWith('touch') ? e.touches[0].clientY : e.clientY;
            this.startX = clientX;
            this.startY = clientY;
            this.startOffsetX = this.offsetX;
            this.startOffsetY = this.offsetY;
            
            // Change cursor during drag
            if (this.$refs.imgContainer) {
                this.$refs.imgContainer.style.cursor = 'grabbing';
            }
        },
        
        moveDrag(e) {
            if (!this.dragging) return;
            const clientX = e.type.startsWith('touch') ? e.touches[0].clientX : e.clientX;
            const clientY = e.type.startsWith('touch') ? e.touches[0].clientY : e.clientY;
            const deltaX = clientX - this.startX;
            const deltaY = clientY - this.startY;
            
            this.updateOffset(this.startOffsetX + deltaX, this.startOffsetY + deltaY);
        },
        
        endDrag() {
            this.dragging = false;
            
            // Reset cursor after drag
            if (this.$refs.imgContainer) {
                this.$refs.imgContainer.style.cursor = this.zoom > 1 ? 'grab' : 'default';
            }
        },
        
        updateOffset(newX, newY) {
            // Calculate bounds based on zoom level and container size
            const maxOffsetX = Math.max(0, ((this.imgW * this.zoom) - this.containerW) / 2);
            const maxOffsetY = Math.max(0, ((this.imgH * this.zoom) - this.containerH) / 2);
            
            // Limit movement within bounds
            this.offsetX = this.clamp(newX, -maxOffsetX, maxOffsetX);
            this.offsetY = this.clamp(newY, -maxOffsetY, maxOffsetY);
        },
        
        adjustOffsets() {
            // Recalculate offsets when container or zoom changes
            this.updateOffset(this.offsetX, this.offsetY);
        },
        
        handleWheel(e) {
            // Zoom with mouse wheel
            const delta = -Math.sign(e.deltaY) * 0.1;
            const newZoom = this.clamp(this.zoom + delta, this.minZoom, this.maxZoom);
            
            if (newZoom !== this.zoom) {
                // Get mouse position relative to image
                const rect = this.$refs.imgContainer.getBoundingClientRect();
                const mouseX = e.clientX - rect.left;
                const mouseY = e.clientY - rect.top;
                
                // Calculate zoom point (center if not over image)
                const zoomPointX = mouseX - this.containerW / 2;
                const zoomPointY = mouseY - this.containerH / 2;
                
                // Previous distance from zoom point
                const prevDistX = zoomPointX * this.zoom;
                const prevDistY = zoomPointY * this.zoom;
                
                // Set new zoom
                this.zoom = newZoom;
                
                // New distance from zoom point
                const newDistX = zoomPointX * this.zoom;
                const newDistY = zoomPointY * this.zoom;
                
                // Adjust offset to zoom into mouse position
                this.updateOffset(
                    this.offsetX + (newDistX - prevDistX) / this.zoom,
                    this.offsetY + (newDistY - prevDistY) / this.zoom
                );
            }
        },
        
        zoomIn() {
            const newZoom = this.clamp(this.zoom + 0.25, this.minZoom, this.maxZoom);
            if (newZoom !== this.zoom) {
                this.zoom = newZoom;
                this.adjustOffsets();
            }
        },
        
        zoomOut() {
            const newZoom = this.clamp(this.zoom - 0.25, this.minZoom, this.maxZoom);
            if (newZoom !== this.zoom) {
                this.zoom = newZoom;
                this.adjustOffsets();
            }
        },
        
        zoomReset() {
            this.zoom = 1;
            this.offsetX = 0;
            this.offsetY = 0;
        },
        
        zoomToFit() {
            if (!this.imgW || !this.imgH || !this.containerW || !this.containerH) return;
            
            // Calculate zoom to fit image in container
            const horizontalRatio = this.containerW / this.imgW;
            const verticalRatio = this.containerH / this.imgH;
            
            // Use the smaller ratio to ensure the image fits entirely
            let fitZoom = Math.min(horizontalRatio, verticalRatio);
            
            // Don't allow zoom greater than 1 for "fit"
            fitZoom = Math.min(fitZoom, 1);
            
            this.zoom = this.clamp(fitZoom, this.minZoom, this.maxZoom);
            this.offsetX = 0;
            this.offsetY = 0;
        }
    };
}