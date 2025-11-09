<!-- Modal para editar usuario -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editUserModalLabel">Editar Usuario</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="routes/users/editUser.php" method="post" enctype="multipart/form-data" id="editUserForm">
                    <input type="hidden" name="codeuser" id="edit_codeuser">
                    
                    <div class="elementoForm">
                        <label for="edit_username" class="labelForm">Nombre Completo:</label>
                        <input type="text" name="username" id="edit_username" class="form-control" required>
                    </div>

                    <div class="elementoForm">
                        <label for="edit_userci" class="labelForm">C.I.:</label>
                        <input type="text" name="userci" id="edit_userci" class="form-control" required>
                    </div>

                    <div class="elementoForm">
                        <label for="edit_userphone" class="labelForm">Teléfono:</label>
                        <input type="text" name="userphone" id="edit_userphone" class="form-control">
                    </div>

                    <div class="elementoForm">
                        <label for="edit_useraddress" class="labelForm">Dirección:</label>
                        <input type="text" name="useraddress" id="edit_useraddress" class="form-control">
                    </div>

                    <div class="elementoForm">
                        <label for="edit_usertype" class="labelForm">Tipo de Usuario:</label>
                        <select name="usertype" id="edit_usertype" class="form-select" required>
                            <!-- Las opciones se cargarán dinámicamente -->
                        </select>
                    </div>

                    <div class="elementoForm">
                        <label for="edit_userlogin" class="labelForm">Login:</label>
                        <input type="text" name="userlogin" id="edit_userlogin" class="form-control" required>
                    </div>

                    <div class="elementoForm">
                        <label for="edit_userpassword" class="labelForm">Contraseña:</label>
                        <input type="password" name="userpassword" id="edit_userpassword" class="form-control" required>
                    </div>

                    <div class="elementoForm">
                        <label for="edit_useraccess" class="labelForm">Acceso:</label>
                        <input type="text" name="useraccess" id="edit_useraccess" class="form-control" required>
                    </div>

                    <div class="elementoForm">
                        <label for="edit_userstate" class="labelForm">Estado:</label>
                        <select name="userstate" id="edit_userstate" class="form-select" required>
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>

                    <div class="mb-3 text-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk"></i> Actualizar
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function cargarTiposDeUsuarioEdit() {
    fetch('../../routes/users/getCategorias.php')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('edit_usertype');
            select.innerHTML = '';
            data.categorias.forEach(categoria => {
                const option = document.createElement('option');
                option.value = categoria.codecategory;
                option.textContent = categoria.namecategory;
                select.appendChild(option);
            });
        })
        .catch(error => console.error('Error al cargar categorías:', error));
}

function cargarDatosUsuario(codeuser) {
    // Primero cargar los tipos de usuario
    fetch('../../routes/users/getCategorias.php')
        .then(response => response.json())
        .then(categoriasData => {
            const select = document.getElementById('edit_usertype');
            select.innerHTML = '';
            categoriasData.categorias.forEach(categoria => {
                const option = document.createElement('option');
                option.value = categoria.codecategory;
                option.textContent = categoria.namecategory;
                select.appendChild(option);
            });
            
            // Luego cargar los datos del usuario
            return fetch(`../../routes/users/getUser.php?id=${codeuser}`);
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.usuario) {
                const usuario = data.usuario;
                document.getElementById('edit_codeuser').value = usuario.codeuser;
                document.getElementById('edit_username').value = usuario.username || '';
                document.getElementById('edit_userci').value = usuario.userci || '';
                document.getElementById('edit_userphone').value = usuario.userphone || '';
                document.getElementById('edit_useraddress').value = usuario.useraddress || '';
                document.getElementById('edit_userlogin').value = usuario.userlogin || '';
                document.getElementById('edit_userpassword').value = usuario.userpassword || '';
                document.getElementById('edit_useraccess').value = usuario.useraccess || '';
                document.getElementById('edit_userstate').value = usuario.userstate || 'Activo';
                document.getElementById('edit_usertype').value = usuario.usertype || '';
            } else {
                console.error('Error al cargar datos del usuario:', data.error);
            }
        })
        .catch(error => console.error('Error al cargar datos del usuario:', error));
}

const editUserModal = document.getElementById('editUserModal');
if (editUserModal) {
    editUserModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const codeuser = button.getAttribute('data-bs-id');
        if (codeuser) {
            cargarDatosUsuario(codeuser);
        }
    });
}
</script>

<style>
  .elementoForm{
      font-size:1rem;
      display:flex;
      justify-content: space-between;
      margin:5px 0px;
      align-items:center;
  }
  .elementoForm input{
      padding:3px;
      width:250px;
  }
  .elementoForm select{
      width:250px;
  }
  .labelForm {
      margin:0;
  }
</style>
