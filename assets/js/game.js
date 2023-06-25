/**
 * This script let's the user cycle through their previous input actions in the Adventure Game
 * by pressing the up and down arrow keys.
 */

(function() {
    // Retrive element
    const entriesElement = document.getElementById('entries-data');

    // Exit early if not found, this most likely means that the user is not on the game page
    if (!entriesElement) {
        return;
    }

    // Retrieve the logged input entries
    const entriesData = entriesElement.getAttribute('data-entries');
    const entries = JSON.parse(entriesData); // (The entries are json encoded)

    let currentIndex = entries.length;

    // Capture keydown event on the input field
    document.querySelector('input[name="input"]').addEventListener('keydown', function(event) {
        // Improve cross-browser compatibility for CJKT users by ignoring all keydown events that are part of composition
        if (event.isComposing || event.keyCode === 229) {
            return;
        }

        // Check if the up arrow key is pressed
        if (event.keyCode === 38) {
            event.preventDefault(); // Prevent the default action of the up arrow key
        
            if (currentIndex > 0) {
                currentIndex--; // Decrement the currentIndex
                
                // Get the latest entry
                const latestEntry = entries[currentIndex];

                // Set the latest entry as the value of the input field
                this.value = latestEntry;
            }

            return;
        }

        // Check if the down arrow key is pressed
        if (event.keyCode === 40) {
            event.preventDefault(); // Prevent the default action of the down arrow key

            if (currentIndex < entries.length - 1) {
                currentIndex++; // Increment the currentIndex

                // Get the next entry
                const nextEntry = entries[currentIndex];

                // Set the next entry as the value of the input field
                this.value = nextEntry;

                return;
            }
            // Empty the input field if there are no more actions to cycle through
            this.value = '';
            currentIndex = entries.length; // Reset currentIndex

            return;
        }
    });
})();
