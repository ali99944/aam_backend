// public/js/admin/json-editor.js

// Global variable to track next field ID to ensure uniqueness within the editor instance
let nextFieldId = 0;
let currentFormId = null; // Track the form being edited

/**
 * Initializes the JSON editor for a given form and populates it with initial data.
 * @param {string} formId The ID of the form containing the editor.
 * @param {object|array} initialContent The initial JSON content (as JS object/array).
 */
function initializeJsonEditor(formId, initialContent = {}) {
    console.log("Initializing editor for form:", formId, "with data:", initialContent);
    currentFormId = formId;
    const formElement = document.getElementById(formId);
    if (!formElement) {
        console.error(`Form with ID "${formId}" not found.`);
        return;
    }

    const container = formElement.querySelector('#dynamic-fields-container');
    const addFieldBtn = formElement.querySelector('#add-content-field-btn');
    const togglePreviewBtn = formElement.querySelector('#toggle-json-preview-btn');
    const jsonPreview = formElement.querySelector('#json-preview');

    if (!container || !addFieldBtn || !togglePreviewBtn || !jsonPreview) {
         console.error("Editor elements (container, add button, preview button, preview area) not found within the form:", formId);
         return;
    }

    // Clear existing fields before initializing (important when modal reopens)
    container.innerHTML = '';
    nextFieldId = 0; // Reset field ID counter for this instance


    // Populate with initial data
    if (initialContent && typeof initialContent === 'object') {
        Object.entries(initialContent).forEach(([key, value]) => {
            addFieldRow(formElement, key, value);
        });
    }

    // Add Field Button Listener (specific to this form)
    // Remove previous listener if exists to prevent duplicates
    const newAddFieldBtn = addFieldBtn.cloneNode(true); // Clone to remove old listeners
    addFieldBtn.parentNode.replaceChild(newAddFieldBtn, addFieldBtn);
    newAddFieldBtn.addEventListener('click', () => addFieldRow(formElement));

    // Remove Button Listener (Event Delegation on container)
    container.removeEventListener('click', handleRemoveField); // Remove previous if any
    container.addEventListener('click', handleRemoveField);

    // Type Change Listener (Event Delegation)
    container.removeEventListener('change', handleTypeChange); // Remove previous if any
    container.addEventListener('change', handleTypeChange);

    // List Item Management Listener (Event Delegation)
    container.removeEventListener('click', handleListItemManagement); // Remove previous if any
    container.addEventListener('click', handleListItemManagement);

    // Toggle Preview Button Listener (specific to this form)
     const newTogglePreviewBtn = togglePreviewBtn.cloneNode(true);
     togglePreviewBtn.parentNode.replaceChild(newTogglePreviewBtn, togglePreviewBtn);
     newTogglePreviewBtn.addEventListener('click', () => {
        const currentJson = buildJsonFromEditor(formId);
        jsonPreview.textContent = JSON.stringify(currentJson, null, 2); // Pretty print
        jsonPreview.style.display = jsonPreview.style.display === 'none' ? 'block' : 'none';
     });

     // Hide preview initially
     jsonPreview.style.display = 'none';
}


// --- Event Handler Functions ---

function handleRemoveField(event) {
    if (event.target.closest('.remove-field-btn')) {
        event.target.closest('.dynamic-field-row').remove();
    }
}

function handleTypeChange(event) {
     if (event.target.classList.contains('field-type-selector')) {
        const selectedType = event.target.value;
        const valueContainer = event.target.closest('.dynamic-field-row').querySelector('.field-value-container');
        renderValueInput(valueContainer, selectedType); // Render with no initial value
    }
}

 function handleListItemManagement(event) {
    const parentForm = event.target.closest('form');
     if (!parentForm) return;

     const formId = parentForm.id;

    // Add list item
    if (event.target.classList.contains('add-list-item-btn')) {
         const listContainer = event.target.previousElementSibling; // The div holding list items
         const itemTemplate = document.getElementById('list-string-item-template').innerHTML;
         listContainer.insertAdjacentHTML('beforeend', itemTemplate);
    }
    // Remove list item
    if (event.target.closest('.remove-list-item-btn')) {
         event.target.closest('.list-item').remove();
    }
}


// --- Core Functions ---

/**
 * Adds a new dynamic field row to the editor.
 * @param {HTMLElement} formElement The form element.
 * @param {string|null} key The initial key (for populating).
 * @param {*} value The initial value (for populating).
 */
function addFieldRow(formElement, key = null, value = null) {
    const container = formElement.querySelector('#dynamic-fields-container');
    const fieldTemplate = document.getElementById('json-field-template').innerHTML;
    const fieldId = nextFieldId++;

    const newFieldHtml = fieldTemplate.replace(/__FIELD_ID__/g, fieldId);
    container.insertAdjacentHTML('beforeend', newFieldHtml);

    const newRow = container.querySelector(`.dynamic-field-row[data-field-id="${fieldId}"]`);
    const typeSelector = newRow.querySelector('.field-type-selector');
    const keyInput = newRow.querySelector('.field-key-input');
    const valueContainer = newRow.querySelector('.field-value-container');

    let determinedType = 'text';
    if (value !== null) {
        determinedType = determineType(value);
        keyInput.value = key || ''; // Populate key
    }

    typeSelector.value = determinedType; // Set the dropdown
    renderValueInput(valueContainer, determinedType, value); // Render and populate value

}

/**
 * Determines the field type based on the JavaScript value type.
 * @param {*} value
 * @returns {string} Field type (e.g., 'text', 'number', 'boolean', 'list_string', 'json')
 */
function determineType(value) {
    if (typeof value === 'boolean') return 'boolean';
    if (typeof value === 'number') return 'number';
    if (typeof value === 'string') {
         // Could add checks for image/file paths here if needed
         // Simple check for potential large text
         return value.length > 100 || value.includes('\n') ? 'textarea' : 'text';
    }
    if (Array.isArray(value)) {
        // Simple check: if all items are strings (or empty array) -> list_string
         if (value.every(item => typeof item === 'string')) {
             return 'list_string';
        }
        // Add logic for list_object if implementing
        // Otherwise, default to raw JSON for complex arrays/objects
        return 'json';
    }
    if (typeof value === 'object' && value !== null) {
        // Default complex objects to raw JSON for simplicity
        return 'json';
    }
    return 'text'; // Default fallback
}

/**
 * Renders the appropriate value input(s) based on the selected field type.
 * @param {HTMLElement} container The container div for value inputs.
 * @param {string} type The selected field type.
 * @param {*} [initialValue=null] The initial value to populate.
 */
function renderValueInput(container, type, initialValue = null) {
    container.innerHTML = '<label class="form-label form-label-sm">Value</label>'; // Reset with label
    let inputHtml = '';

    switch (type) {
        case 'number':
            inputHtml = `<input type="number" class="form-control form-control-sm field-value-input" step="any" value="${initialValue !== null ? initialValue : ''}">`;
            break;
        case 'boolean':
            inputHtml = `<div class="form-check form-switch pt-1">
                            <input class="form-check-input field-value-input" type="checkbox" role="switch" ${initialValue ? 'checked' : ''}>
                         </div>`;
            break;
         case 'textarea':
             inputHtml = `<textarea class="form-control form-control-sm field-value-input" rows="3">${initialValue !== null ? initialValue : ''}</textarea>`;
             break;
        case 'image':
             // Store path in hidden input, show file input and preview
             inputHtml = `<input type="hidden" class="field-value-input current-file-path" value="${initialValue || ''}">`; // Stores the actual path/value
             inputHtml += `<input type="file" class="form-control form-control-sm file-input mb-1" accept="image/*">`;
             if (initialValue) {
                // Assuming initialValue is a URL accessible via storage link
                inputHtml += `<div class="mt-1 img-preview"><img src="${initialValue}" style="max-height: 60px; border-radius: 3px;" alt="Preview"> <button type="button" class="btn btn-xs btn-outline-danger remove-current-file-btn ms-1">X</button></div>`;
             }
             // JS needs to update hidden input if new file selected, or clear it if removed
            break;
         case 'list_string':
            inputHtml = `<div class="list-string-container border p-2 rounded"></div>
                         <button type="button" class="btn btn-xs btn-outline-secondary mt-1 add-list-item-btn">Add Item</button>`;
            container.innerHTML += inputHtml; // Add container first
            // Populate list items
            const listContainer = container.querySelector('.list-string-container');
            if (Array.isArray(initialValue)) {
                initialValue.forEach(item => {
                    const itemTemplate = document.getElementById('list-string-item-template').innerHTML;
                    listContainer.insertAdjacentHTML('beforeend', itemTemplate);
                    listContainer.lastElementChild.querySelector('.list-item-input').value = item;
                });
            }
             return; // Return early as population is done
        case 'json':
             inputHtml = `<textarea class="form-control form-control-sm field-value-input" rows="4" placeholder='Enter valid JSON, e.g., {"key": "value", "items": []}'>${initialValue !== null ? JSON.stringify(initialValue, null, 2) : ''}</textarea>`;
             break;
        case 'text':
        default:
            inputHtml = `<input type="text" class="form-control form-control-sm field-value-input" value="${initialValue !== null ? initialValue : ''}">`;
            break;
    }
    container.innerHTML += inputHtml; // Append the generated input(s)

     // Add listeners for specific types like image removal
     if (type === 'image') {
         container.querySelector('.file-input')?.addEventListener('change', handleFileChange);
         container.querySelector('.remove-current-file-btn')?.addEventListener('click', handleRemoveCurrentFile);
     }

}

/**
 * Handles new file selection for image/file types.
 * For now, we only store the filename in the hidden input for simplicity.
 * A real implementation might do async uploads here or handle files server-side.
 */
function handleFileChange(event) {
    const fileInput = event.target;
    const valueContainer = fileInput.closest('.field-value-container');
    const hiddenInput = valueContainer.querySelector('.current-file-path');
    const previewContainer = valueContainer.querySelector('.img-preview');

    if (fileInput.files && fileInput.files[0]) {
        const fileName = fileInput.files[0].name; // Just use filename for now
         // Store a marker or filename. Backend needs to check `request->file()` based on this.
         hiddenInput.value = `__UPLOAD__:${fileName}`; // Simple marker

        // Optional: Show local preview (doesn't reflect saved state yet)
         if (previewContainer) previewContainer.remove(); // Remove old server preview
        const reader = new FileReader();
        reader.onload = function (e) {
             const localPreview = document.createElement('div');
             localPreview.className = 'mt-1 img-preview-local';
             localPreview.innerHTML = `<img src="${e.target.result}" style="max-height: 60px; border-radius: 3px;" alt="Local Preview">`;
             fileInput.insertAdjacentElement('afterend', localPreview);
         }
         reader.readAsDataURL(fileInput.files[0]);

    } else {
         // No file selected, potentially revert hidden input? Depends on desired UX.
         // hiddenInput.value = ''; // Or maybe revert to original value if editing
    }
}

 /** Handles clicking the 'X' button next to an existing image preview */
 function handleRemoveCurrentFile(event) {
     const button = event.target;
     const valueContainer = button.closest('.field-value-container');
     const hiddenInput = valueContainer.querySelector('.current-file-path');
     const previewContainer = button.closest('.img-preview');
     const fileInput = valueContainer.querySelector('.file-input');
     const localPreview = valueContainer.querySelector('.img-preview-local');

     hiddenInput.value = '__DELETE__'; // Special marker for backend to delete file
     if (previewContainer) previewContainer.remove();
     if (localPreview) localPreview.remove();
     if (fileInput) fileInput.value = ''; // Clear file input
 }


/**
 * Builds the final JSON object from the current state of the editor fields.
 * @param {string} formId The ID of the form containing the editor.
 * @returns {object} The constructed JSON data.
 */
function buildJsonFromEditor(formId) {
    const jsonData = {};
    const formElement = document.getElementById(formId);
    if (!formElement) return {};

    const container = formElement.querySelector('#dynamic-fields-container');
    const fieldRows = container.querySelectorAll('.dynamic-field-row');
    let isValid = true; // Flag for basic validation

    fieldRows.forEach(row => {
        const keyInput = row.querySelector('.field-key-input');
        const typeSelector = row.querySelector('.field-type-selector');
        const valueContainer = row.querySelector('.field-value-container');
        const valueInput = valueContainer.querySelector('.field-value-input'); // Might be input, textarea, or checkbox
        const keyError = row.querySelector('.key-error');

        const key = keyInput.value.trim();
        const type = typeSelector.value;

        // Basic Key Validation
        keyError.style.display = 'none'; // Reset error
        if (!key) {
             keyInput.classList.add('is-invalid');
             keyError.textContent = 'Key cannot be empty.';
             keyError.style.display = 'block';
             isValid = false;
             return; // Skip this row
        }
         if (jsonData.hasOwnProperty(key)) {
             keyInput.classList.add('is-invalid');
             keyError.textContent = 'Key must be unique.';
             keyError.style.display = 'block';
             isValid = false;
             return; // Skip this row
        }
        keyInput.classList.remove('is-invalid');


        let value;
        switch (type) {
            case 'number':
                value = valueInput ? parseFloat(valueInput.value) : null;
                if (isNaN(value)) value = null; // Handle invalid number input
                break;
            case 'boolean':
                value = valueInput ? valueInput.checked : false;
                break;
             case 'image':
             case 'file': // Treat file similarly for now, store path/marker
                 value = valueInput ? valueInput.value : null; // Value comes from the hidden input
                 // Backend needs to check for '__UPLOAD__:' or '__DELETE__' markers
                 break;
             case 'list_string':
                value = [];
                const listItems = valueContainer.querySelectorAll('.list-item-input');
                listItems.forEach(itemInput => {
                    if (itemInput.value.trim() !== '') { // Only add non-empty items
                        value.push(itemInput.value.trim());
                    }
                });
                break;
             case 'json':
                try {
                    value = valueInput ? JSON.parse(valueInput.value || '{}') : {};
                } catch (e) {
                    console.warn(`Invalid JSON provided for key "${key}":`, valueInput.value);
                     value = {}; // Default to empty object on parse error
                     // TODO: Add visual validation feedback for JSON textarea
                }
                break;
            case 'text':
             case 'textarea':
            default:
                value = valueInput ? valueInput.value : '';
                break;
        }
        jsonData[key] = value;
    });

     if (!isValid) {
         // Optionally show a general error message
         console.error("JSON Editor has validation errors. Cannot build final JSON.");
        // Maybe throw an error to prevent form submission in prepareSectionJson?
         throw new Error("Editor validation failed.");
     }

    console.log("Built JSON:", jsonData);
    return jsonData;
}