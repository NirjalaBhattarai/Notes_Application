// notes-manager.js
let currentNotes = [];

async function loadNotes() {
    try {
        const response = await fetch('/api/notes', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Accept': 'application/json'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            currentNotes = data.notes;
            displayNotes(currentNotes);
        }
    } catch (error) {
        console.error('Error loading notes:', error);
    }
}

function displayNotes(notes) {
    const container = document.getElementById('notes-container');
    container.innerHTML = notes.map(note => `
        <div class="note-card">
            <div class="note-title">${note.title}</div>
            <div class="note-content">${note.content.substring(0, 100)}...</div>
            <div class="note-actions">
                <button onclick="editNote(${note.id})">Edit</button>
                <button onclick="deleteNote(${note.id})">Delete</button>
            </div>
        </div>
    `).join('');
}

function showAddNoteForm() {
    document.getElementById('add-note-modal').classList.remove('hidden');
}

function hideModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadNotes();
    
    // Handle form submission
    document.getElementById('note-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        // Add your form submission logic here
    });
});