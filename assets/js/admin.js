jQuery(document).ready(function($) {
    'use strict';
    
    let mediaUploader;
    let isEditing = false;
    let editingLogoId = null;
    
    // Initialize
    init();
    
    function init() {
        loadLogos();
        bindEvents();
    }
    
    function bindEvents() {
        // Image upload
        $('#select-image-btn').on('click', handleImageUpload);
        $('#remove-image-btn').on('click', removeImage);
        
        // Form submission
        $('#lsfd-logo-form').on('submit', handleFormSubmit);
        
        // Cancel edit
        $('#cancel-btn').on('click', cancelEdit);
        
        // Logo actions (delegated events)
        $(document).on('click', '.lsfd-edit-btn', handleEdit);
        $(document).on('click', '.lsfd-delete-btn', handleDelete);
    }
    
    function handleImageUpload(e) {
        e.preventDefault();
        
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        
        mediaUploader = wp.media({
            title: 'Choose Logo Image',
            button: { text: 'Use This Image' },
            multiple: false,
            library: { type: 'image' }
        });
        
        mediaUploader.on('select', function() {
            const attachment = mediaUploader.state().get('selection').first().toJSON();
            setSelectedImage(attachment.url);
        });
        
        mediaUploader.open();
    }
    
    function setSelectedImage(url) {
        $('#logo_image').val(url);
        $('#image-preview').html('<img src="' + url + '" alt="Selected image" />');
        $('#select-image-btn').text('Change Image');
        $('#remove-image-btn').show();
    }
    
    function removeImage() {
        $('#logo_image').val('');
        $('#image-preview').html(
            '<div class="lsfd-placeholder">' +
            '<div class="dashicons dashicons-format-image"></div>' +
            '<p>No image selected</p>' +
            '</div>'
        );
        $('#select-image-btn').text('Select Image');
        $('#remove-image-btn').hide();
    }
    
    function handleFormSubmit(e) {
        e.preventDefault();
        
        const formData = {
            action: isEditing ? 'lsfd_update_logo' : 'lsfd_save_logo',
            nonce: lsfd_ajax.nonce,
            logo_title: $('#logo_title').val().trim(),
            logo_image: $('#logo_image').val().trim(),
            logo_url: $('#logo_url').val().trim(),
            logo_alt: $('#logo_alt').val().trim()
        };
        
        if (isEditing) {
            formData.logo_id = editingLogoId;
        }
        
        // Validation
        if (!formData.logo_title) {
            showMessage('Please enter a logo title.', 'error');
            return;
        }
        
        if (!formData.logo_image) {
            showMessage('Please select an image.', 'error');
            return;
        }
        
        const submitBtn = $('#submit-btn');
        const originalText = submitBtn.text();
        
        submitBtn.prop('disabled', true).text(
            isEditing ? lsfd_ajax.strings.updating : lsfd_ajax.strings.saving
        );
        
        $.ajax({
            url: lsfd_ajax.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    showMessage(response.data.message, 'success');
                    resetForm();
                    loadLogos();
                } else {
                    showMessage(response.data.message || 'An error occurred.', 'error');
                }
            },
            error: function() {
                showMessage('Network error occurred.', 'error');
            },
            complete: function() {
                submitBtn.prop('disabled', false).text(originalText);
            }
        });
    }
    
    function handleEdit() {
        const logoCard = $(this).closest('.lsfd-logo-card');
        const logoData = logoCard.data('logo');
        
        // Populate form
        $('#logo_id').val(logoData.id);
        $('#logo_title').val(logoData.title);
        $('#logo_image').val(logoData.image);
        $('#logo_url').val(logoData.url);
        $('#logo_alt').val(logoData.alt);
        
        // Update image preview
        if (logoData.image) {
            setSelectedImage(logoData.image);
        }
        
        // Switch to edit mode
        isEditing = true;
        editingLogoId = logoData.id;
        
        $('#form-title').text('Edit Logo');
        $('#submit-btn').text('Update Logo');
        $('#cancel-btn').show();
        
        // Scroll to form
        $('html, body').animate({
            scrollTop: $('.lsfd-form-section').offset().top - 50
        }, 300);
    }
    
    function handleDelete() {
        const logoCard = $(this).closest('.lsfd-logo-card');
        const logoData = logoCard.data('logo');
        
        if (!confirm(lsfd_ajax.strings.confirm_delete)) {
            return;
        }
        
        $.ajax({
            url: lsfd_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'lsfd_delete_logo',
                nonce: lsfd_ajax.nonce,
                logo_id: logoData.id
            },
            success: function(response) {
                if (response.success) {
                    showMessage(response.data.message, 'success');
                    loadLogos();
                } else {
                    showMessage(response.data.message || 'Failed to delete logo.', 'error');
                }
            },
            error: function() {
                showMessage('Network error occurred.', 'error');
            }
        });
    }
    
    function cancelEdit() {
        resetForm();
        isEditing = false;
        editingLogoId = null;
    }
    
    function resetForm() {
        $('#lsfd-logo-form')[0].reset();
        $('#logo_id').val('');
        removeImage();
        
        $('#form-title').text('Add New Logo');
        $('#submit-btn').text('Add Logo');
        $('#cancel-btn').hide();
        
        isEditing = false;
        editingLogoId = null;
    }
    
    function loadLogos() {
        $('#logos-container').html('<div class="lsfd-loading"></div>');
        
        $.ajax({
            url: lsfd_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'lsfd_get_logos',
                nonce: lsfd_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    displayLogos(response.data);
                } else {
                    $('#logos-container').html('<div class="lsfd-no-logos">Error loading logos.</div>');
                }
            },
            error: function() {
                $('#logos-container').html('<div class="lsfd-no-logos">Network error occurred.</div>');
            }
        });
    }
    
    function displayLogos(logos) {
        if (!logos || logos.length === 0) {
            $('#logos-container').html(
                '<div class="lsfd-no-logos">' +
                'No logos found. Add your first logo using the form above!' +
                '</div>'
            );
            return;
        }
        
        let html = '<div class="lsfd-logos-grid" id="sortable-logos">';
        
        logos.forEach(function(logo) {
            html += '<div class="lsfd-logo-card" data-logo-id="' + logo.id + '">';
            html += '<div class="lsfd-drag-handle"></div>';
            html += '<div class="lsfd-logo-image">';
            html += '<img src="' + logo.image + '" alt="' + (logo.alt || logo.title) + '" />';
            html += '</div>';
            html += '<div class="lsfd-logo-info">';
            html += '<h4>' + logo.title + '</h4>';
            if (logo.url) {
                html += '<div class="lsfd-logo-url">' + logo.url + '</div>';
            }
            html += '<div class="lsfd-logo-actions">';
            html += '<button type="button" class="button lsfd-edit-btn">Edit</button>';
            html += '<button type="button" class="button lsfd-delete-btn">Delete</button>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
        });
        
        html += '</div>';
        
        $('#logos-container').html(html);
        
        // Store logo data for easy access
        logos.forEach(function(logo) {
            $('[data-logo-id="' + logo.id + '"]').data('logo', logo);
        });
        
        // Initialize sortable
        initSortable();
    }
    
    function initSortable() {
        $('#sortable-logos').sortable({
            items: '.lsfd-logo-card',
            handle: '.lsfd-drag-handle',
            placeholder: 'lsfd-sortable-placeholder',
            tolerance: 'pointer',
            update: function(event, ui) {
                const logoOrder = [];
                $('.lsfd-logo-card').each(function() {
                    logoOrder.push($(this).data('logo-id'));
                });
                
                saveSortOrder(logoOrder);
            }
        });
    }
    
    function saveSortOrder(logoOrder) {
        $.ajax({
            url: lsfd_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'lsfd_reorder_logos',
                nonce: lsfd_ajax.nonce,
                logo_order: logoOrder
            },
            success: function(response) {
                if (response.success) {
                    // Optional: show brief success message
                } else {
                    showMessage('Failed to save order.', 'error');
                    // Reload to restore original order
                    loadLogos();
                }
            },
            error: function() {
                showMessage('Network error while saving order.', 'error');
                loadLogos();
            }
        });
    }
    
    function showMessage(message, type) {
        // Remove existing messages
        $('.lsfd-message').remove();
        
        const messageHtml = '<div class="lsfd-message ' + type + '">' + message + '</div>';
        $('.lsfd-form-section').prepend(messageHtml);
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            $('.lsfd-message').fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
        
        // Scroll to message
        $('html, body').animate({
            scrollTop: $('.lsfd-message').offset().top - 50
        }, 300);
    }
});
