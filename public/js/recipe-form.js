/**
 * Recipe Form Management
 * Handles adding, removing, and rearranging cooking steps and ingredients in recipe forms
 */
class RecipeStepsManager {
    constructor() {
        this.stepsContainer = document.getElementById('steps-container');
        this.addStepBtn = document.getElementById('add-step');
        this.stepIndex = this.getInitialStepIndex();
        this.draggedElement = null;

        if (this.stepsContainer && this.addStepBtn) {
            this.init();
        }
    }

    init() {
        this.addStepBtn.addEventListener('click', () => this.addStep());
        this.stepsContainer.addEventListener('click', (e) => this.handleRemoveStep(e));
        this.makeStepsSortable();
    }

    getInitialStepIndex() {
        // Get the initial step index from the data attribute
        const initialCount = this.stepsContainer.getAttribute('data-initial-count');
        return initialCount ? parseInt(initialCount, 10) : 0;
    }

    addStep() {
        const stepItem = document.createElement('div');
        stepItem.className = 'step-item flex items-start gap-2';
        stepItem.draggable = true;

        stepItem.innerHTML = `
            <div class="flex items-center gap-2">
                <div class="drag-handle cursor-move text-gray-400 hover:text-gray-600" title="Drag to reorder">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </div>
                <div class="step-number w-6 h-6 bg-primary-color text-white rounded-full flex items-center justify-center text-sm font-semibold">
                    ${this.stepIndex + 1}
                </div>
            </div>
            <div class="flex-1">
                <textarea name="recipe[steps][${this.stepIndex}]" placeholder="Enter a cooking step..." class="form-control" rows="2"></textarea>
            </div>
            <button type="button" class="btn-icon btn-icon-danger remove-step" title="Remove step">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        `;

        this.stepsContainer.appendChild(stepItem);
        this.stepIndex++;
        this.updateStepNumbers();
        this.setupDragEvents(stepItem);
    }

    makeStepsSortable() {
        // Make existing steps draggable
        const existingSteps = this.stepsContainer.querySelectorAll('.step-item');
        existingSteps.forEach(step => {
            step.draggable = true;
            this.setupDragEvents(step);
        });
    }

    setupDragEvents(element) {
        element.addEventListener('dragstart', (e) => this.handleDragStart(e));
        element.addEventListener('dragover', (e) => this.handleDragOver(e));
        element.addEventListener('drop', (e) => this.handleDrop(e));
        element.addEventListener('dragend', (e) => this.handleDragEnd(e));
        element.addEventListener('dragenter', (e) => this.handleDragEnter(e));
        element.addEventListener('dragleave', (e) => this.handleDragLeave(e));
    }

    handleDragStart(e) {
        this.draggedElement = e.target;
        e.target.style.opacity = '0.5';
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', e.target.outerHTML);
    }

    handleDragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
    }

    handleDragEnter(e) {
        e.preventDefault();
        if (e.target.classList.contains('step-item') && e.target !== this.draggedElement) {
            e.target.classList.add('drag-over');
        }
    }

    handleDragLeave(e) {
        e.target.classList.remove('drag-over');
    }

    handleDrop(e) {
        e.preventDefault();
        e.target.classList.remove('drag-over');

        if (e.target.classList.contains('step-item') && e.target !== this.draggedElement) {
            const dropTarget = e.target;
            const container = this.stepsContainer;

            // Determine if we should insert before or after the drop target
            const rect = dropTarget.getBoundingClientRect();
            const midpoint = rect.top + rect.height / 2;
            const insertBefore = e.clientY < midpoint;

            if (insertBefore) {
                container.insertBefore(this.draggedElement, dropTarget);
            } else {
                container.insertBefore(this.draggedElement, dropTarget.nextSibling);
            }

            this.updateStepNumbers();
        }
    }

    handleDragEnd(e) {
        e.target.style.opacity = '';
        this.draggedElement = null;

        // Remove all drag-over classes
        const dragOverElements = this.stepsContainer.querySelectorAll('.drag-over');
        dragOverElements.forEach(el => el.classList.remove('drag-over'));
    }

    updateStepNumbers() {
        const steps = this.stepsContainer.querySelectorAll('.step-item');
        steps.forEach((step, index) => {
            const numberElement = step.querySelector('.step-number');
            if (numberElement) {
                numberElement.textContent = index + 1;
            }

            // Update the textarea name attribute to reflect new order
            const textarea = step.querySelector('textarea');
            if (textarea) {
                textarea.name = `recipe[steps][${index}]`;
            }
        });
    }

    handleRemoveStep(e) {
        if (e.target.closest('.remove-step')) {
            const stepItem = e.target.closest('.step-item');
            stepItem.remove();
            this.updateStepNumbers();
        }
    }
}

/**
 * Recipe Ingredients Management
 * Handles adding, removing, and rearranging ingredients in recipe forms
 */
class RecipeIngredientsManager {
    constructor() {
        this.ingredientsContainer = document.getElementById('ingredients-container');
        this.addIngredientBtn = document.getElementById('add-ingredient');
        this.ingredientIndex = this.getInitialIngredientIndex();
        this.draggedElement = null;

        if (this.ingredientsContainer && this.addIngredientBtn) {
            this.init();
        }
    }

    init() {
        this.addIngredientBtn.addEventListener('click', () => this.addIngredient());
        this.ingredientsContainer.addEventListener('click', (e) => this.handleRemoveIngredient(e));
        this.makeIngredientsSortable();
    }

    getInitialIngredientIndex() {
        // Get the initial ingredient index from the data attribute
        const initialCount = this.ingredientsContainer.getAttribute('data-initial-count');
        return initialCount ? parseInt(initialCount, 10) : 0;
    }

    addIngredient() {
        // Get the prototype HTML from the data attribute
        const prototype = this.ingredientsContainer.getAttribute('data-prototype');
        if (!prototype) {
            console.error('No prototype found for ingredients collection');
            return;
        }

        // Replace __name__ placeholder with the current index
        const newIngredientHtml = prototype.replace(/__name__/g, this.ingredientIndex);

        // Create a wrapper div for the ingredient
        const ingredientItem = document.createElement('div');
        ingredientItem.className = 'ingredient-item flex items-start gap-2';
        ingredientItem.draggable = true;

        ingredientItem.innerHTML = `
            <div class="flex items-center gap-2">
                <div class="drag-handle cursor-move text-gray-400 hover:text-gray-600" title="Drag to reorder">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </div>
                <div class="ingredient-number w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-semibold">
                    ${this.ingredientIndex + 1}
                </div>
            </div>
            <div class="flex-1 grid grid-cols-3 gap-2">
                ${newIngredientHtml}
            </div>
            <button type="button" class="btn-icon btn-icon-danger remove-ingredient" title="Remove ingredient">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        `;

        this.ingredientsContainer.appendChild(ingredientItem);
        this.ingredientIndex++;
        this.updateIngredientNumbers();
        this.setupDragEvents(ingredientItem);
    }

    makeIngredientsSortable() {
        // Make existing ingredients draggable
        const existingIngredients = this.ingredientsContainer.querySelectorAll('.ingredient-item');
        existingIngredients.forEach(ingredient => {
            ingredient.draggable = true;
            this.setupDragEvents(ingredient);
        });
    }

    setupDragEvents(element) {
        element.addEventListener('dragstart', (e) => this.handleDragStart(e));
        element.addEventListener('dragover', (e) => this.handleDragOver(e));
        element.addEventListener('drop', (e) => this.handleDrop(e));
        element.addEventListener('dragend', (e) => this.handleDragEnd(e));
        element.addEventListener('dragenter', (e) => this.handleDragEnter(e));
        element.addEventListener('dragleave', (e) => this.handleDragLeave(e));
    }

    handleDragStart(e) {
        this.draggedElement = e.target;
        e.target.style.opacity = '0.5';
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', e.target.outerHTML);
    }

    handleDragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
    }

    handleDragEnter(e) {
        e.preventDefault();
        if (e.target.classList.contains('ingredient-item') && e.target !== this.draggedElement) {
            e.target.classList.add('drag-over');
        }
    }

    handleDragLeave(e) {
        e.target.classList.remove('drag-over');
    }

    handleDrop(e) {
        e.preventDefault();
        e.target.classList.remove('drag-over');

        if (e.target.classList.contains('ingredient-item') && e.target !== this.draggedElement) {
            const dropTarget = e.target;
            const container = this.ingredientsContainer;

            // Determine if we should insert before or after the drop target
            const rect = dropTarget.getBoundingClientRect();
            const midpoint = rect.top + rect.height / 2;
            const insertBefore = e.clientY < midpoint;

            if (insertBefore) {
                container.insertBefore(this.draggedElement, dropTarget);
            } else {
                container.insertBefore(this.draggedElement, dropTarget.nextSibling);
            }

            this.updateIngredientNumbers();
        }
    }

    handleDragEnd(e) {
        e.target.style.opacity = '';
        this.draggedElement = null;

        // Remove all drag-over classes
        const dragOverElements = this.ingredientsContainer.querySelectorAll('.drag-over');
        dragOverElements.forEach(el => el.classList.remove('drag-over'));
    }

    updateIngredientNumbers() {
        const ingredients = this.ingredientsContainer.querySelectorAll('.ingredient-item');
        ingredients.forEach((ingredient, index) => {
            const numberElement = ingredient.querySelector('.ingredient-number');
            if (numberElement) {
                numberElement.textContent = index + 1;
            }

            // Update the form field name attributes to reflect new order
            const foodItemInput = ingredient.querySelector('input[name*="[foodItemName]"]');
            const amountInput = ingredient.querySelector('input[name*="[amount]"]');
            const unitSelect = ingredient.querySelector('select[name*="[unit]"]');

            if (foodItemInput) foodItemInput.name = `recipe[ingredients][${index}][foodItemName]`;
            if (amountInput) amountInput.name = `recipe[ingredients][${index}][amount]`;
            if (unitSelect) unitSelect.name = `recipe[ingredients][${index}][unit]`;
        });
    }

    handleRemoveIngredient(e) {
        if (e.target.closest('.remove-ingredient')) {
            const ingredientItem = e.target.closest('.ingredient-item');
            ingredientItem.remove();
            this.updateIngredientNumbers();
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new RecipeStepsManager();
    new RecipeIngredientsManager();
});
