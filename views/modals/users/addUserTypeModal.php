<!-- Modal para gestionar tipos de usuario -->
<div class="modal fade" id="addUserType" tabindex="-1" aria-labelledby="addUserTypeLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addUserTypeLabel">Gestionar Tipos de Usuario</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Formulario para agregar nuevo tipo -->
                <div class="mb-4">
                    <h5>Agregar Nuevo Tipo de Usuario</h5>
                    <form action="routes/users/addCategory.php" method="post" id="addCategoryForm">
                        <div class="elementoForm">
                            <label for="namecategory" class="labelForm">Nombre del Tipo:</label>
                            <input type="text" name="namecategory" id="namecategory" class="form-control" required>
                        </div>
                        <div class="mb-3 text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-plus"></i> Agregar
                            </button>
                        </div>
                    </form>
                </div>

                <hr>

                <!-- Lista de tipos existentes -->
                <div>
                    <h5>Tipos de Usuario Existentes</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped" id="tablaCategorias">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Los datos se cargarán dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar tipo de usuario -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editCategoryModalLabel">Editar Tipo de Usuario</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="routes/users/editCategory.php" method="post" id="editCategoryForm">
                    <input type="hidden" name="codecategory" id="edit_codecategory">
                    <div class="elementoForm">
                        <label for="edit_namecategory" class="labelForm">Nombre del Tipo:</label>
                        <input type="text" name="namecategory" id="edit_namecategory" class="form-control" required>
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

<!-- Modal para eliminar tipo de usuario -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="deleteCategoryModalLabel">Aviso</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Desea eliminar este tipo de usuario?
            </div>
            <div class="modal-footer">
                <form action="routes/users/deleteCategory.php" method="post">
                    <input type="hidden" name="codecategory" id="delete_codecategory">
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function cargarCategorias() {
    fetch('../../routes/users/getCategorias.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector('#tablaCategorias tbody');
            tbody.innerHTML = '';
            
            data.categorias.forEach(categoria => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${categoria.codecategory}</td>
                    <td>${categoria.namecategory}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editCategoryModal" 
                                data-bs-id="${categoria.codecategory}"
                                data-bs-name="${categoria.namecategory}">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" 
                                data-bs-toggle="modal" 
                                data-bs-target="#deleteCategoryModal" 
                                data-bs-id="${categoria.codecategory}">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(error => console.error('Error al cargar categorías:', error));
}

// Cargar categorías cuando se abre el modal
const addUserTypeModal = document.getElementById('addUserType');
if (addUserTypeModal) {
    addUserTypeModal.addEventListener('show.bs.modal', cargarCategorias);
}

// Manejar el modal de editar categoría
const editCategoryModal = document.getElementById('editCategoryModal');
if (editCategoryModal) {
    editCategoryModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const codecategory = button.getAttribute('data-bs-id');
        const namecategory = button.getAttribute('data-bs-name');
        
        document.getElementById('edit_codecategory').value = codecategory;
        document.getElementById('edit_namecategory').value = namecategory || '';
    });
}

// Manejar el modal de eliminar categoría
const deleteCategoryModal = document.getElementById('deleteCategoryModal');
if (deleteCategoryModal) {
    deleteCategoryModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const codecategory = button.getAttribute('data-bs-id');
        document.getElementById('delete_codecategory').value = codecategory;
    });
}

// Recargar categorías después de agregar una nueva
const addCategoryForm = document.getElementById('addCategoryForm');
if (addCategoryForm) {
    addCategoryForm.addEventListener('submit', function(e) {
        // El formulario se enviará normalmente, pero podríamos hacer una petición AJAX aquí si queremos
        // Por ahora, dejamos que se recargue la página después del submit
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

