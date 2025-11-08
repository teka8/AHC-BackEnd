/**
 * Custom Image Resize Module for Quill 2.0
 * Compatible with Quill 2.x without external dependencies
 */

class ImageResize {
    constructor(quill, options = {}) {
        this.quill = quill;
        this.options = options;
        this.selectedImage = null;
        this.overlay = null;
        this.handles = [];
        this.startX = 0;
        this.startY = 0;
        this.startWidth = 0;
        this.startHeight = 0;
        
        this.init();
    }
    
    init() {
        // Add click handler to editor
        this.quill.root.addEventListener('click', (e) => {
            if (e.target && e.target.tagName === 'IMG') {
                this.showResizeHandles(e.target);
            } else {
                this.hideResizeHandles();
            }
        });
        
        // Hide handles when clicking outside
        document.addEventListener('click', (e) => {
            if (!this.quill.root.contains(e.target)) {
                this.hideResizeHandles();
            }
        });
        
        // Hide handles on scroll
        this.quill.root.addEventListener('scroll', () => {
            if (this.overlay) {
                this.hideResizeHandles();
            }
        });
    }
    
    showResizeHandles(img) {
        this.hideResizeHandles();
        this.selectedImage = img;
        
        // Create overlay
        this.overlay = document.createElement('div');
        this.overlay.style.cssText = `
            position: absolute;
            border: 2px solid #06c;
            box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
            cursor: move;
            z-index: 100;
        `;
        
        // Position overlay over image
        this.positionOverlay();
        
        // Create resize handles
        this.createHandles();
        
        // Add to DOM
        document.body.appendChild(this.overlay);
        
        // Update position on window resize
        window.addEventListener('resize', this.positionOverlay.bind(this));
        window.addEventListener('scroll', this.positionOverlay.bind(this), true);
    }
    
    positionOverlay() {
        if (!this.selectedImage || !this.overlay) return;
        
        const rect = this.selectedImage.getBoundingClientRect();
        this.overlay.style.left = rect.left + window.scrollX + 'px';
        this.overlay.style.top = rect.top + window.scrollY + 'px';
        this.overlay.style.width = rect.width + 'px';
        this.overlay.style.height = rect.height + 'px';
    }
    
    createHandles() {
        const positions = ['nw', 'ne', 'sw', 'se'];
        const cursors = {
            'nw': 'nwse-resize',
            'ne': 'nesw-resize',
            'sw': 'nesw-resize',
            'se': 'nwse-resize'
        };
        
        positions.forEach(pos => {
            const handle = document.createElement('div');
            handle.className = `resize-handle resize-handle-${pos}`;
            handle.style.cssText = `
                position: absolute;
                width: 12px;
                height: 12px;
                background: white;
                border: 2px solid #06c;
                border-radius: 50%;
                cursor: ${cursors[pos]};
                z-index: 101;
            `;
            
            // Position handles
            if (pos.includes('n')) handle.style.top = '-6px';
            if (pos.includes('s')) handle.style.bottom = '-6px';
            if (pos.includes('w')) handle.style.left = '-6px';
            if (pos.includes('e')) handle.style.right = '-6px';
            
            handle.addEventListener('mousedown', (e) => this.startResize(e, pos));
            
            this.overlay.appendChild(handle);
            this.handles.push(handle);
        });
        
        // Add size display
        const sizeDisplay = document.createElement('div');
        sizeDisplay.className = 'resize-size-display';
        sizeDisplay.style.cssText = `
            position: absolute;
            bottom: -30px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            pointer-events: none;
        `;
        sizeDisplay.textContent = `${Math.round(this.selectedImage.width)} × ${Math.round(this.selectedImage.height)}`;
        this.overlay.appendChild(sizeDisplay);
        this.sizeDisplay = sizeDisplay;
    }
    
    startResize(e, position) {
        e.preventDefault();
        e.stopPropagation();
        
        this.startX = e.clientX;
        this.startY = e.clientY;
        this.startWidth = this.selectedImage.width;
        this.startHeight = this.selectedImage.height;
        this.resizePosition = position;
        
        const handleMouseMove = (e) => this.handleResize(e);
        const handleMouseUp = () => {
            document.removeEventListener('mousemove', handleMouseMove);
            document.removeEventListener('mouseup', handleMouseUp);
            this.positionOverlay();
        };
        
        document.addEventListener('mousemove', handleMouseMove);
        document.addEventListener('mouseup', handleMouseUp);
    }
    
    handleResize(e) {
        if (!this.selectedImage) return;
        
        const deltaX = e.clientX - this.startX;
        const deltaY = e.clientY - this.startY;
        
        let newWidth = this.startWidth;
        let newHeight = this.startHeight;
        
        // Calculate new dimensions based on handle position
        if (this.resizePosition.includes('e')) {
            newWidth = this.startWidth + deltaX;
        } else if (this.resizePosition.includes('w')) {
            newWidth = this.startWidth - deltaX;
        }
        
        // Maintain aspect ratio
        const aspectRatio = this.startWidth / this.startHeight;
        newHeight = newWidth / aspectRatio;
        
        // Set minimum size
        const minSize = 50;
        if (newWidth < minSize) {
            newWidth = minSize;
            newHeight = minSize / aspectRatio;
        }
        
        // Apply new dimensions
        this.selectedImage.width = newWidth;
        this.selectedImage.height = newHeight;
        this.selectedImage.style.width = newWidth + 'px';
        this.selectedImage.style.height = 'auto';
        
        // Update overlay and size display
        this.positionOverlay();
        if (this.sizeDisplay) {
            this.sizeDisplay.textContent = `${Math.round(newWidth)} × ${Math.round(newHeight)}`;
        }
    }
    
    hideResizeHandles() {
        if (this.overlay) {
            this.overlay.remove();
            this.overlay = null;
        }
        this.handles = [];
        this.selectedImage = null;
        this.sizeDisplay = null;
    }
}

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ImageResize;
}

// Make globally available
window.QuillImageResize = ImageResize;
