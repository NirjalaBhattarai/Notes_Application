<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Notes App</title>
<link rel="stylesheet" href="/css/notes.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="container">

<header>
    <div class="app-title">Notes App</div>
    <div class="auth-section">
        <div id="guestButtons">


    <a href="/login" class="btn btn-login">Login</a>
            <a href="/register" class="btn btn-register">Register</a>
        </div>
        <div id="userButtons" style="display: none;">
            <span class="user-info">
                <span class="welcome-text">Welcome!</span>
                <span id="userName">Guest</span>
            </span>
            <div class="button">
        <button class="btn btn-logout" id="logoutBtn">Logout</button>
           </div>
        </div>
    </div>
</header>


<div class="content">
    <div class="main-content">
        <div id="alertMessage" class="alert"></div>
      <div class="notes-header">
        <div class="search-container">
             
        
        <input type="text" id="searchInput" placeholder="Search notes..." class="search-box">
                <div id="clearSearch" style="display:none;"></div>
         
            </div>
            <button class="btn btn-add" id="addNoteBtn">Add Note</button>
   </div>
        <div class="category_filter" id="categoryFilter"></div>
      
        <div id="searchStatus" class="search-status" style="display:none;"></div>
        <div id="notesGrid" class="notes-grid"></div>
    </div>
</div>


<div id="addNoteModal" class="modal">
    <div class="modal-content">
     
    <h2 id="modalTitle">Add Note</h2>
    
        <form id="noteForm">
            <input type="hidden" id="noteId">
            <div class="form-group">
                              <label for="noteTitle">Title</label>
                <input type="text" id="noteTitle" required>
            </div>
                      <div class="form-group">
                <label for="noteContent">Content</label>
                <textarea id="noteContent" placeholder="Write your note here..." required></textarea>
            </div>
            <div class="form-group">
                <label for="noteCategory">Category</label>
            <select id="noteCategory">
                    <option value="">Select Category</option>
                </select>
            </div>
         <div class="modal-actions">
                <button type="button" class="btn btn-logout" id="cancelNote">Cancel</button>
           
         <button type="submit" class="btn btn-add" id="saveNoteBtn">Save Note</button>
            </div>
        </form>
    </div>
</div>

<!-- delete section -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
  <h2>Confirm Delete</h2>
        <p>Are you sure you want to delete this note? 
        
        <div class="modal-actions">
            <button type="button" class="btn-cancel" id="cancelDelete">Cancel</button>
          
            <button type="button" class="btn-confirm" id="confirmDelete">Delete</button>
        </div>
    </div>
</div>

<script>
let config = {
    apiUrl: "http://localhost:8000/api",
    defaultCats: [
        {id:1,name:"Work"}, 
        {id:2,name:"Personal"},
         {id:3,name:"Ideas"}, 
         {id:4,name:"Tasks"}
    ]
};

let state = {
    notes: [],
     categories: [],
      logged:false, 
      activeCat:null, 
      searching:false,
       pendingDel:null
};

document.addEventListener("DOMContentLoaded", () => startApp());

function startApp() {

    checkLogin(); //check if user is logged in

    fetchCategories();//fetch categories



    fetchNotes();//loads notes

    bindUI();// at last set up button to work
}



//check if user is logged in by looking for token

function checkLogin() {
    const token = localStorage.getItem("token");
    let userRaw = localStorage.getItem("user");
    if(token && userRaw) {

        try {
            let userData = JSON.parse(userRaw);// store ths user data stores as json str
            state.logged = true;
            showLoggedIn(userData);
        }
         catch(e) { console.log("Invalid user data", e); logout(); }
    } else showGuest();
}




function showGuest() {

    document.getElementById("guestButtons").style.display="flex";

    document.getElementById("userButtons").style.display="none";
    state.logged=false;
}



function showLoggedIn(user) {


    document.getElementById("guestButtons").style.display="none";
  
    document.getElementById("userButtons").style.display="flex";

    document.getElementById("userName").textContent=user.name || "User";
    state.logged=true;

}





async function fetchCategories() {
    try {
        const res = await fetch(`${config.apiUrl}/categories`);
        state.categories = res.ok ? await res.json() : config.defaultCats;//if fails to fetch from backend then fetch from here
    } catch(e) { state.categories = config.defaultCats; }

    populateCategoryDropdown();
    renderCategoryButtons();

}

function populateCategoryDropdown() {
    const sel = document.getElementById("noteCategory");
    sel.innerHTML = '<option value="">Select Category</option>';
    state.categories.forEach(c => {

        let opt = document.createElement("option");
        opt.value = c.id;

        opt.textContent = c.name;

        sel.appendChild(opt);
    });
}

function renderCategoryButtons() {
    const container = document.getElementById("categoryFilter");
    container.innerHTML = '';
    state.categories.forEach(c => {
        const btn = document.createElement("button");

        btn.className="category-btn";
        btn.dataset.category=c.id;


        btn.textContent=c.name;

        btn.addEventListener("click", () => switchCategory(c.id));
        container.appendChild(btn);
    });
    if(state.categories.length && state.activeCat===null){
        state.activeCat = state.categories[0].id;
        highlightActiveCategory();
    }
}

function switchCategory(catId){
    resetSearch();
    state.activeCat=catId;
    highlightActiveCategory();

    renderNotes();
}

function highlightActiveCategory(){
    document.querySelectorAll(".category-btn").forEach(b=>b.classList.remove("active"));
    const active = document.querySelector(`.category-btn[data-category="${state.activeCat}"]`);
    if(active) active.classList.add("active");
}

async function fetchNotes(){

    if(!state.logged){ 
        showLoginPrompt(); 
        return;
     }

    try{
        const token = localStorage.getItem("token");
        const res = await fetch(`${config.apiUrl}/notes`, {// this callls you r backend api
            headers: {
                 "Authorization": `Bearer ${token}`,//this aattcah token so that i knows which user you are asking
                  "Accept":"application/json" // i want response in json format
                }
        });

        if(res.ok){ let data = await res.json(); 
            state.notes=Array.isArray(data)?data:[];
             renderNotes();
            
            }

        else throw new Error();

    } catch(e){ showError("Failed to load notes"); showEmptyNotes(); }
}


function renderNotes(){
    if(state.searching) return;
    const list = state.activeCat ? state.notes.filter(n => n.category_id == state.activeCat) : state.notes;
    drawNotes(list);
}

function drawNotes(lst){
    const grid = document.getElementById("notesGrid");
    if(!lst || !lst.length){
        grid.innerHTML = state.searching ? '<div class="empty-state"><h3>No notes found</h3><p>Try another search.</p></div>' 
            : '<div class="empty-state"><h3>No notes yet</h3><p>Click "Add Note" to start.</p></div>';
        return;
    }
    grid.innerHTML = lst.map(n => `
        <div class="note-card">
            <div class="note-title">${safe(n.title||"Untitled")}</div>
            <div class="note-content">${safe(n.content||"No content")}</div>

            <div class="note-meta"><span class="note-category">${safe(categoryName(n.category_id))}</span>    <span>${formatDate(n.created_at)}</span></div>
            <div class="note-actions">
                <button class="btn-edit" onclick="editNote(${n.id})"><i class="fas fa-edit"></i> Edit</button>

                <button class="btn_delete" onclick="askDelete(${n.id})"><i class="fas fa-trash"></i> Delete</button>
            </div>
        </div>
    `).join('');
}

function categoryName(id){ const c = state.categories.find(x=>x.id==id); return c ? c.name : "General"; }

function bindUI(){
    document.getElementById("noteForm").addEventListener("submit", saveNote);

 document.getElementById("addNoteBtn").addEventListener("click", openNewNote);    document.getElementById("logoutBtn").addEventListener("click", logout);

    document.getElementById("cancelNote").addEventListener("click", closeNoteModal);
    document.getElementById("cancelDelete").addEventListener("click", closeDeleteModal);
  document.getElementById("clearSearch").addEventListener("click", resetSearch);
  document.getElementById("confirmDelete").addEventListener("click", deleteNote);
    document.getElementById("searchInput").addEventListener("input", searchNotes);
 window.addEventListener("click", e => { if(e.target.classList.contains("modal")) closeModal(e.target.id); });

}

function searchNotes(){
    const q=document.getElementById("searchInput").value.trim().toLowerCase();
    const clear=document.getElementById("clearSearch");

 if(!q){ 
    state.searching=false; 
    clear.style.display="none"; 
    renderNotes(); 
    return; }

    state.searching=true;

 clear.style.display="inline-block";
    const results = state.notes.filter(n => (n.title||"").toLowerCase().includes(q));
    drawNotes(results);
}

function resetSearch(){ 
    document.getElementById("searchInput").value="";
     state.searching=false; 
     document.getElementById("clearSearch").style.display="none"; 
     renderNotes(); }

function openNewNote(){ 
    clearForm();
     document.getElementById("modalTitle").textContent="Add Note"; 
     document.getElementById("saveNoteBtn").textContent="Save Note";
      showModal("addNoteModal");
    
    }

function editNote(id){ 
    
    const n=state.notes.find(x=>x.id===id);
     if(!n) return; document.getElementById("noteId").value=n.id;
      document.getElementById("noteTitle").value=n.title||""; 
      document.getElementById("noteContent").value=n.content||""; 
      document.getElementById("noteCategory").value=n.category_id||""; 
      document.getElementById("modalTitle").textContent="Edit Note"; 
      document.getElementById("saveNoteBtn").textContent="Update Note"; 
      showModal("addNoteModal"); 
    }

function askDelete(id){ 
    state.pendingDel=id; 
    showModal("deleteModal"); 
}

async function deleteNote(){

    if(!state.pendingDel) return;

    try{
        const token = localStorage.getItem("token");
        const res = await fetch(`${config.apiUrl}/notes/${state.pendingDel}`, {method:"DELETE", headers:{Authorization:`Bearer ${token}`}});
        if(res.ok){ closeDeleteModal(); fetchNotes(); showSuccess("Note deleted."); } else throw new Error();
    } catch(e){ 
        showError("Failed to delete."); 
    }
    finally{ state.pendingDel=null; }
}


async function saveNote(e){
    e.preventDefault();
    if(!state.logged){ showError("Login required."); location.href="/login"; return; }
  
    const title=document.getElementById("noteTitle").value.trim();
       const content=document.getElementById("noteContent").value.trim();
  
       const catId=document.getElementById("noteCategory").value;
    const noteId=document.getElementById("noteId").value;

    if(!title||!content){ 
        showError("Title and content required."); 
        return; }


    try{
        const token=localStorage.getItem("token");//taking token form browser
        const url=noteId?`${config.apiUrl}/notes/${noteId}`:`${config.apiUrl}/notes`;

        const method=noteId?"PUT":"POST";

        const data={title,content}; if(catId) data.category_id=parseInt(catId);
        const res = await fetch(url,{method,headers:{Authorization:`Bearer ${token}`,
            "Content-Type":"application/json"},body:JSON.stringify(data)});
        
        if(res.ok){ closeNoteModal(); clearForm(); fetchNotes(); 
            showSuccess(noteId?"Note updated!":"Note saved!"); } else throw new Error();
   
    } catch(e){ showError("Failed to save note."); }
}

function showModal(id){ document.getElementById(id).style.display="flex"; }
function closeModal(id){ document.getElementById(id).style.display="none"; }
function closeNoteModal(){ closeModal("addNoteModal"); }
function closeDeleteModal(){ closeModal("deleteModal"); }

function clearForm(){ 
    document.getElementById("noteForm").reset(); 
    document.getElementById("noteId").value=""; }

function logout(){ 
    if(confirm("Are you sure?")) doLogout(); }



function doLogout(){
     localStorage.removeItem("token"); 
     localStorage.removeItem("user"); 
     showGuest(); fetchNotes(); 
     showSuccess("Logged out."); }

function showLoginPrompt(){ 
    document.getElementById("notesGrid").innerHTML='<div class="empty-state"><h3>Please login to see your notes</h3><p>Login to create and manage your notes</p></div>'; }


function showEmptyNotes(){ 
    document.getElementById("notesGrid").innerHTML='<div class="empty-state"><h3>No notes yet</h3><p>Click "Add Note" to create your first note!</p></div>'; }

function showError(msg){ showAlert(msg,"error"); }

function showSuccess(msg){ showAlert(msg,"success"); }

function showAlert(msg,type){ const el=document.getElementById("alertMessage"); el.textContent=msg; el.className=`alert alert-${type}`; el.style.display="block"; setTimeout(()=>el.style.display="none",5000); }

function safe(txt){ const d=document.createElement("div"); d.textContent=txt||""; return d.innerHTML; }
function formatDate(d){ if(!d) return "Unknown date"; try{ return new Date(d).toLocaleDateString(); } catch(e){ return "Invalid date"; } }
</script>
</body>
</html>
