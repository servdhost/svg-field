(function() {
    'use strict';

    function initSvgField(container) {
        const uploadArea = container.querySelector('.svg-upload-area');
        const fileInput = container.querySelector('.svg-file-input');
        const contentInput = container.querySelector('.svg-content-input');
        const removeBtn = container.querySelector('.svg-remove-btn');
        const replaceBtn = container.querySelector('.svg-replace-btn');

        if (!uploadArea || !fileInput || !contentInput) return;

        // Handle click to upload
        uploadArea.addEventListener('click', function(e) {
            if (e.target.closest('.svg-actions')) return;
            fileInput.click();
        });

        // Handle file selection
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type === 'image/svg+xml') {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const svgContent = e.target.result;
                    if (isValidSvg(svgContent)) {
                        contentInput.value = svgContent;
                        updatePreview(container, svgContent);
                    } else {
                        alert('Please select a valid SVG file.');
                    }
                };
                reader.readAsText(file);
            }
        });

        // Handle drag and drop
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                if (file.type === 'image/svg+xml') {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const svgContent = e.target.result;
                        if (isValidSvg(svgContent)) {
                            contentInput.value = svgContent;
                            updatePreview(container, svgContent);
                        } else {
                            alert('Please select a valid SVG file.');
                        }
                    };
                    reader.readAsText(file);
                }
            }
        });

        // Handle remove button
        if (removeBtn) {
            removeBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                contentInput.value = '';
                updatePreview(container, '');
            });
        }

        // Handle replace button
        if (replaceBtn) {
            replaceBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                fileInput.click();
            });
        }
    }

    function updatePreview(container, svgContent) {
        const uploadArea = container.querySelector('.svg-upload-area');
        const contentInput = container.querySelector('.svg-content-input');
        
        if (svgContent) {
            uploadArea.innerHTML = `
                <div class="svg-preview-container">
                    <div class="svg-preview">
                        ${svgContent}
                    </div>
                    <div class="svg-actions">
                        <button type="button" class="btn small svg-remove-btn">Remove</button>
                        <button type="button" class="btn small svg-replace-btn">Replace</button>
                    </div>
                </div>
                <input type="file" class="svg-file-input" accept=".svg,image/svg+xml" style="display: none;">
                ${contentInput.outerHTML}
            `;
        } else {
            uploadArea.innerHTML = `
                <div class="svg-upload-prompt">
                    <div class="svg-upload-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21,15 16,10 5,21"/>
                        </svg>
                    </div>
                    <p>Click to upload an SVG file or drag and drop</p>
                </div>
                <input type="file" class="svg-file-input" accept=".svg,image/svg+xml" style="display: none;">
                ${contentInput.outerHTML}
            `;
        }
        
        // Update the content input value after DOM update
        const newContentInput = container.querySelector('.svg-content-input');
        if (newContentInput) {
            newContentInput.value = svgContent || '';
        }
        
        // Re-initialize event listeners for the new elements
        initSvgField(container);
    }

    function isValidSvg(content) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(content, 'image/svg+xml');
        return doc.documentElement.tagName === 'svg';
    }

    // Initialize all SVG fields on page load
    document.addEventListener('DOMContentLoaded', function() {
        const svgFields = document.querySelectorAll('.svg-field');
        svgFields.forEach(initSvgField);
    });

    // Initialize SVG fields when new ones are added dynamically
    if (typeof Garnish !== 'undefined') {
        Garnish.$doc.on('afterInit', '.svg-field', function() {
            initSvgField(this);
        });
    }
})();