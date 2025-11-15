<?php
include($_SERVER['DOCUMENT_ROOT'] . '/config/sessionController.php');
$subpagina = isset($_GET['s']) ? $_GET['s'] : '1';
?>

<h2 class="titulogestion">Vista Trabajadores</h2>
<section class="containerOptions">
    <div class="inputArea">
        <label for="buscar">Buscar</label>
        <input type="text" id="buscar" class="form-control" placeholder="Por nombre o CI">
    </div>
    <div class="inputArea">
        <label for="registrosPorPagina">Numero de Registros</label>
        <select id="registrosPorPagina" class="form-control">
            <option value="10">10</option>
            <option value="30">30</option>
            <option value="50">50</option>
        </select>
    </div>

    <button class="btnOption2" data-bs-toggle="modal" data-bs-target="#addWorkerModal">
        <i class="fa-solid fa-circle-plus"></i> Nuevo Registro
    </button>

    <i class="fas fa-sync-alt refresh-icon" onclick="actualizarPagina()"></i>


<div class="containerBtnsSubPag">
    <button type="button" class="btn-subpagina" id="btn_1" onclick="cambiarSubpagina('1')">
        <i class="fas fa-indent"></i>
    </button>
    <button type="button" class="btn-subpagina" id="btn_2" onclick="cambiarSubpagina('2')">
        <i class="fas fa-th-large"></i>
    </button>
</div>

</section>



<!-- Contenido de Subp��gina 1 -->
<div id="subpagina1" style="display: <?php echo ($subpagina == '1') ? 'block' : 'none'; ?>;">
    <!--Tabla de trabajadores-->
<div class="containerTable">
    <table class="table table-sm table-striped table-hover" id="tablaProductos">
        <thead>
            <tr>
                <th scope="col">Código</th>
                <th scope="col">Nombres</th>
                <th scope="col">CI</th>
                <th scope="col">Cargo</th>
                <th scope="col">Fecha Ingreso</th>
                <th scope="col">Sexo</th>
                <th scope="col">Estado</th>
                <th scope="col">Acciones</th>
            </tr>
        </thead>
        <tbody>
             <!--Filas de datos--> 
         </tbody>
    </table>
</div>
</div>

<!-- Contenido de Subp��gina 2 -->
<div id="subpagina2" style="display: <?php echo ($subpagina == '2') ? 'block' : 'none'; ?>;">
    <div id="htmlDatos"></div>
</div>


   <!--Paginación-->
<nav>
    <ul class="pagination justify-content-center" id="pagination">
          <!--Páginas dinámicas-->
    </ul>
        </nav>

<script>
function cambiarSubpagina(subpagina) {
    history.pushState(null, "", "?p=trabajadores&s=" + subpagina);
    document.getElementById("subpagina1").style.display = (subpagina === "1") ? "block" : "none";
    document.getElementById("subpagina2").style.display = (subpagina === "2") ? "block" : "none";
    pintarBtnSub();
}
function pintarBtnSub() {
    const params = new URLSearchParams(window.location.search);
    const subpagina = params.get('s');
    document.querySelectorAll(".btn-subpagina").forEach(btn => {
        btn.classList.remove("pintado");
    });
    const btnId = (subpagina === '1' || subpagina === '2') ? subpagina : '1';
    document.getElementById("btn_" + btnId).classList.add("pintado");
}
document.addEventListener("DOMContentLoaded", pintarBtnSub);

    
</script>



<?php include 'modals/worker/addWorkerModal.php'; ?>
<?php include 'modals/worker/workerview.php'; ?>
<?php include 'modals/worker/deleteWorkerModal.php'; ?>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>

<script>
    const tabla = $('#tablaProductos tbody');
    const paginacion = $('#pagination');
    const buscarInput = $('#buscar');
    const registrosPorPaginaSelect = $('#registrosPorPagina');
    let currentPage = 1;
    let rowsPerPage = parseInt(registrosPorPaginaSelect.val());
    let totalPages = 1;
    let lastWorkers = [];

    registrosPorPaginaSelect.on('change', function () {
        rowsPerPage = parseInt($(this).val());
        currentPage = 1; // Reiniciar a la primera p��gina
        cargarTrabajadores(currentPage, rowsPerPage);
    });

    buscarInput.on('input', function () {
        cargarTrabajadores(1, rowsPerPage);
    });




    async function cargarTrabajadores(pagina = 1, limite = 10) {
        const buscar = buscarInput.val();
        try {
            const response = await fetch(`../routes/workers/getWorkers.php?pagina=${pagina}&limite=${limite}&buscar=${encodeURIComponent(buscar)}`);
            if (!response.ok) {
                throw new Error('Error al cargar los datos');
            }
            const data = await response.json();
            lastWorkers = Array.isArray(data.workers) ? data.workers : [];
            construirTabla(lastWorkers, 'tablaProductos');
            construirHTMLDatos(data.workers);
            construirPaginacion(pagina, data.totalPaginas, limite, 'pagination');
    
        } catch (error) {
            console.error('Error al cargar trabajadores:', error);
            alert('Hubo un error al cargar los trabajadores. Por favor, int��ntalo de nuevo.');
        }
    }
    function construirTabla(workers, tablaId) {
        const tbody = $(`#${tablaId} tbody`);
        tbody.empty();
        workers.forEach(worker => {
            const defaultImg = worker.workersex === 'F' 
                ? '/resource/images/foto-perfil-mujer.avif' 
                : '/resource/images/foto-perfil-hombre.avif';
            const workerImg = worker.workerimg ? `/${worker.workerimg}` : defaultImg;
            const estado = (worker.workerstate === 1 || worker.workerstate === '1' || worker.workerstate === true) ? 'Activo' : 'Inactivo';
            const workerRow = `
                <tr>
                    <td>${worker.workercode}</td>
                    <td>${worker.fullname}</td>
                    <td>${worker.workerdocnumber || '----'}</td>
                    <td>${worker.workerrol || '----'}</td>
                    <td>${worker.workerdateinit || '----'}</td>
                    <td>${worker.workersex || '----'}</td>
                    <td>${estado}</td>
                    <td>
                        <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#workerview" data-bs-id="${worker.codeworker}">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-warning" onclick="getDataWorker('${worker.codeworker}'); return false;">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteWorkerModal" data-bs-id="${worker.codeworker}">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </td>
                </tr>
            `;
            tbody.append(workerRow);
        });
    }
    
    
    //AQUI AGREGAR EL METODO FETCH PARAO BTENER LOS DATOS DEL TRABAJADOR
    function getDataWorker(id) {
        document.getElementById('code_worker').value = id;
        fetch('../routes/workers/workerview.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'codeworker=' + encodeURIComponent(id)
        })
        .then(r => r.json())
        .then(data => {
            if (data && !data.error) {
                const setIf = (elId, val) => { const el = document.getElementById(elId); if (el != null) el.value = val || ''; };
                setIf('workercode', data.workercode);
                setIf('workername1', data.workername1);
                setIf('workername2', data.workername2);
                setIf('workerlastname1', data.workerlastname1);
                setIf('workerlastname2', data.workerlastname2);
                setIf('workerhousbandname', data.workerhousbandname);
                setIf('workerbirthdate', data.workerbirthdate);
                setIf('workertypedoc', data.workertypedoc);
                setIf('workerdoccity', data.workerdoccity);
                setIf('workerdocnumber', data.workerdocnumber);
                setIf('workersecurenum', data.workersecurenum);
                setIf('workercuanum', data.workercuanum);
                setIf('workercity', data.workercity);
                setIf('workeremail', data.workeremail);
                setIf('workerphone1', data.workerphone1);
                setIf('workerphone2', data.workerphone2);
                setIf('codearea', data.codearea || data.workerarea);
                setIf('namearea', data.namearea || data.workerarea_name);
                setIf('codeoccupation', data.codeoccupation || data.workerrol);
                setIf('nameoccupation', data.nameoccupation || data.workerrol_name);
                setIf('codesection', data.codesection || data.workersection);
                setIf('namesection', data.namesection || data.workersection_name);
                setIf('workeraddress', data.workeraddress);
                setIf('workernationality', data.workernationality);
                setIf('workersex', data.workersex);
                setIf('workernit', data.workernit);
                setIf('workercivilstatus', data.workercivilstatus);
                setIf('accountMain', data.accountMain || data.workerbanknum);
                setIf('workerdateinit', data.workerdateinit);
                setIf('workerdateout', data.workerdateout);
                const img = document.getElementById('imgProfile');
                if (img) {
                    const defaultImg = data.workersex === 'F' 
                        ? '/resource/images/foto-perfil-mujer.avif' 
                        : '/resource/images/foto-perfil-hombre.avif';
                    img.src = data.workerimg ? ('/' + data.workerimg) : defaultImg;
                }
                const cuentas = document.getElementById('cuentas-container');
                if (cuentas) {
                    cuentas.innerHTML = '';
                    const accs = Array.isArray(data.accounts) ? data.accounts : [];
                    if (accs.length) {
                        accs.forEach(acc => {
                            const div = document.createElement('div');
                            div.className = 'elementoForm elemDinamico';
                            div.innerHTML = `
                                <div class="elementoForm">
                                    <label class="labelForm">Banco:</label>
                                    <input type="text" class="form-control" name="nameBank[]" value="${acc.accountbank || ''}" required>
                                </div>
                                <div class="elementoForm">
                                    <label class="labelForm">Cuenta:</label>
                                    <input type="text" class="form-control" name="cuentaBank[]" value="${acc.accountnro || ''}" required>
                                </div>
                            `;
                            cuentas.appendChild(div);
                        });
                    } else {
                        const div = document.createElement('div');
                        div.className = 'elementoForm elemDinamico';
                        div.innerHTML = `
                            <div class="elementoForm">
                                <label class="labelForm">Banco:</label>
                                <input type="text" class="form-control" name="nameBank[]" value="" required>
                            </div>
                            <div class="elementoForm">
                                <label class="labelForm">Cuenta:</label>
                                <input type="text" class="form-control" name="cuentaBank[]" value="" required>
                            </div>
                        `;
                        cuentas.appendChild(div);
                    }
                }
                const fams = document.getElementById('familares-container');
                if (fams) {
                    fams.innerHTML = '';
                    const famList = Array.isArray(data.family) ? data.family : [];
                    if (famList.length) {
                        famList.forEach(f => {
                            const div = document.createElement('div');
                            div.className = 'familiar';
                            div.innerHTML = `
                                <div style="background:#fdfdfd; margin:5px; border-radius:7px; padding:10px;">
                                    <div class="elementoForm"><label>Nombre:</label><input type="text" name="familyname[]" value="${f.familyname || ''}" required></div>
                                    <div class="elementoForm"><label>Apellido:</label><input type="text" name="familylastname[]" value="${f.familylastname || ''}" required></div>
                                    <div class="elementoForm"><label>Sexo:</label>
                                        <select name="familysex[]">
                                            <option value="" ${!f.familysex?'selected':''}>Seleccione...</option>
                                            <option value="masculino" ${f.familysex==='masculino'?'selected':''}>Masculino</option>
                                            <option value="femenino" ${f.familysex==='femenino'?'selected':''}>Femenino</option>
                                            <option value="otros" ${f.familysex==='otros'?'selected':''}>Otros</option>
                                        </select>
                                    </div>
                                    <div class="elementoForm"><label>Edad:</label><input type="text" name="familyage[]" value="${f.familyage || ''}" required></div>
                                    <div class="elementoForm"><label>Parentesco:</label>
                                        <select name="familykin[]">
                                            <option value="" ${!f.familykin?'selected':''}>Seleccione...</option>
                                            <option value="padre" ${f.familykin==='padre'?'selected':''}>Padre</option>
                                            <option value="madre" ${f.familykin==='madre'?'selected':''}>Madre</option>
                                            <option value="hijo" ${f.familykin==='hijo'?'selected':''}>Hijo</option>
                                            <option value="hija" ${f.familykin==='hija'?'selected':''}>Hija</option>
                                            <option value="hermano" ${f.familykin==='hermano'?'selected':''}>Hermano</option>
                                            <option value="hermana" ${f.familykin==='hermana'?'selected':''}>Hermana</option>
                                            <option value="abuelo" ${f.familykin==='abuelo'?'selected':''}>Abuelo</option>
                                            <option value="abuela" ${f.familykin==='abuela'?'selected':''}>Abuela</option>
                                            <option value="tio" ${f.familykin==='tio'?'selected':''}>Tío</option>
                                            <option value="tia" ${f.familykin==='tia'?'selected':''}>Tía</option>
                                            <option value="sobrino" ${f.familykin==='sobrino'?'selected':''}>Sobrino</option>
                                            <option value="sobrina" ${f.familykin==='sobrina'?'selected':''}>Sobrina</option>
                                            <option value="primo" ${f.familykin==='primo'?'selected':''}>Primo</option>
                                            <option value="prima" ${f.familykin==='prima'?'selected':''}>Prima</option>
                                            <option value="otros" ${f.familykin==='otros'?'selected':''}>Otros</option>
                                        </select>
                                    </div>
                                </div>
                            `;
                            fams.appendChild(div);
                        });
                    } else {
                        const div = document.createElement('div');
                        div.className = 'familiar';
                        div.innerHTML = `
                            <div style="background:#fdfdfd; margin:5px; border-radius:7px; padding:10px;">
                                <div class="elementoForm"><label>Nombre:</label><input type="text" name="familyname[]" value="" required></div>
                                <div class="elementoForm"><label>Apellido:</label><input type="text" name="familylastname[]" value="" required></div>
                                <div class="elementoForm"><label>Sexo:</label>
                                    <select name="familysex[]">
                                        <option value="" selected>Seleccione...</option>
                                        <option value="masculino">Masculino</option>
                                        <option value="femenino">Femenino</option>
                                        <option value="otros">Otros</option>
                                    </select>
                                </div>
                                <div class="elementoForm"><label>Edad:</label><input type="text" name="familyage[]" value="" required></div>
                                <div class="elementoForm"><label>Parentesco:</label>
                                    <select name="familykin[]">
                                        <option value="" selected>Seleccione...</option>
                                        <option value="padre">Padre</option>
                                        <option value="madre">Madre</option>
                                        <option value="hijo">Hijo</option>
                                        <option value="hija">Hija</option>
                                        <option value="hermano">Hermano</option>
                                        <option value="hermana">Hermana</option>
                                        <option value="abuelo">Abuelo</option>
                                        <option value="abuela">Abuela</option>
                                        <option value="tio">Tío</option>
                                        <option value="tia">Tía</option>
                                        <option value="sobrino">Sobrino</option>
                                        <option value="sobrina">Sobrina</option>
                                        <option value="primo">Primo</option>
                                        <option value="prima">Prima</option>
                                        <option value="otros">Otros</option>
                                    </select>
                                </div>
                            </div>
                        `;
                        fams.appendChild(div);
                    }
                }
                const turnsContainer = document.getElementById('turnos-container');
                if (turnsContainer) {
                    turnsContainer.innerHTML = '';
                    fetch('../routes/workers/getTurns.php')
                        .then(rr => rr.json())
                        .then(allTurns => {
                            let baseIndex = 1000;
                            const turnList = Array.isArray(data.turnw) ? data.turnw : [];
                            if (turnList.length) {
                                turnList.forEach((t, idx) => {
                                    const index = baseIndex + idx;
                                    const wrapper = document.createElement('div');
                                    wrapper.className = 'elementoForm elemDinamico';
                                    wrapper.id = `turno_${index}`;
                                    const options = allTurns.map(turn => {
                                        const isSelected = (String(turn.id) === String(t.codeturn)) || (turn.turn_name === t.turn_name && turn.turn_start === t.turn_start && turn.turn_end === t.turn_end);
                                        return `<option value="${turn.id}" data-start="${turn.turn_start}" data-end="${turn.turn_end}" ${isSelected ? 'selected' : ''}>${turn.turn_name} (${turn.turn_start} - ${turn.turn_end})</option>`;
                                    }).join('');
                                    wrapper.innerHTML = `
                                        <div class="elementoForm">
                                            <label class="labelForm">Turno:</label>
                                            <select class="form-select" name="codeturn[]" onchange="mostrarDetallesTurno(this.value, ${index})">
                                                <option value="">Seleccione una opción</option>
                                                ${options}
                                            </select>
                                        </div>
                                        <div class="elementoForm">
                                            <label class="labelForm">Horario de Inicio de Turno:</label>
                                            <input type="text" class="form-control" name="turnstart[]" readonly value="${t.turn_start || ''}" required>
                                        </div>
                                        <div class="elementoForm">
                                            <label class="labelForm">Horario de Fin de Turno:</label>
                                            <input type="text" class="form-control" name="turnend[]" readonly value="${t.turn_end || ''}" required>
                                        </div>
                                    `;
                                    turnsContainer.appendChild(wrapper);
                                });
                            } else {
                                const index = baseIndex;
                                const wrapper = document.createElement('div');
                                wrapper.className = 'elementoForm elemDinamico';
                                wrapper.id = `turno_${index}`;
                                const options = allTurns.map(turn => (
                                    `<option value="${turn.id}" data-start="${turn.turn_start}" data-end="${turn.turn_end}">${turn.turn_name} (${turn.turn_start} - ${turn.turn_end})</option>`
                                )).join('');
                                wrapper.innerHTML = `
                                    <div class="elementoForm">
                                        <label class="labelForm">Turno:</label>
                                        <select class="form-select" name="codeturn[]" onchange="mostrarDetallesTurno(this.value, ${index})">
                                            <option value="" selected>Seleccione una opción</option>
                                            ${options}
                                        </select>
                                    </div>
                                    <div class="elementoForm">
                                        <label class="labelForm">Horario de Inicio de Turno:</label>
                                        <input type="text" class="form-control" name="turnstart[]" readonly required>
                                    </div>
                                    <div class="elementoForm">
                                        <label class="labelForm">Horario de Fin de Turno:</label>
                                        <input type="text" class="form-control" name="turnend[]" readonly required>
                                    </div>
                                `;
                                turnsContainer.appendChild(wrapper);
                            }
                        });
                }
                const docs = document.getElementById('documents-container');
                if (docs) {
                    docs.innerHTML = '';
                    const docList = Array.isArray(data.documents) ? data.documents : [];
                    if (docList.length) {
                        docList.forEach(doc => {
                            const div = document.createElement('div');
                            div.className = 'document';
                            div.innerHTML = `
                                <div style=\"background:#fdfdfd; margin:5px; border-radius:7px; padding:10px;\">
                                    <div class=\"elementoForm\"><label>Grado de formación</label>
                                        <select name=\"grado-formacion[]\" required>
                                            <option value=\"\" ${doc.gradoFormacion ? '' : 'selected'}>Seleccione...</option>
                                            <option value=\"escuela\" ${doc.gradoFormacion==='escuela'?'selected':''}>Escuela</option>
                                            <option value=\"colegio\" ${doc.gradoFormacion==='colegio'?'selected':''}>Colegio</option>
                                            <option value=\"tecnico\" ${doc.gradoFormacion==='tecnico'?'selected':''}>Técnico</option>
                                            <option value=\"universidad\" ${doc.gradoFormacion==='universidad'?'selected':''}>Universidad</option>
                                            <option value=\"otro\" ${doc.gradoFormacion==='otro'?'selected':''}>Otro</option>
                                        </select>
                                    </div>
                                    <div class=\"elementoForm\"><label>Título</label><input type=\"text\" name=\"titulo[]\" value=\"${doc.titulo || ''}\" required /></div>
                                    <div class=\"elementoForm\"><label>Descripción del curso</label><textarea name=\"descripcion-curso[]\" style=\"width:150px;\">${doc.descripcionCurso || ''}</textarea></div>
                                    <div class=\"elementoForm\"><label>Fecha cursada</label><input type=\"date\" name=\"fecha-cursada[]\" value=\"${doc.fechaCursada || ''}\" /></div>
                                    ${doc.urlCertificado ? `<div class=\"elementoForm\"><a href=\"${doc.urlCertificado}\" target=\"_blank\">Ver certificado</a></div>` : ''}
                                </div>
                            `;
                            docs.appendChild(div);
                        });
                    } else {
                        const div = document.createElement('div');
                        div.className = 'document';
                        div.innerHTML = `
                            <div style=\"background:#fdfdfd; margin:5px; border-radius:7px; padding:10px;\">
                                <div class=\"elementoForm\"><label>Grado de formación</label>
                                    <select name=\"grado-formacion[]\" required>
                                        <option value=\"\" selected>Seleccione...</option>
                                        <option value=\"escuela\">Escuela</option>
                                        <option value=\"colegio\">Colegio</option>
                                        <option value=\"tecnico\">Técnico</option>
                                        <option value=\"universidad\">Universidad</option>
                                        <option value=\"otro\">Otro</option>
                                    </select>
                                </div>
                                <div class=\"elementoForm\"><label>Título</label><input type=\"text\" name=\"titulo[]\" value=\"\" required /></div>
                                <div class=\"elementoForm\"><label>Descripción del curso</label><textarea name=\"descripcion-curso[]\" style=\"width:150px;\"></textarea></div>
                                <div class=\"elementoForm\"><label>Fecha cursada</label><input type=\"date\" name=\"fecha-cursada[]\" value=\"\" /></div>
                            </div>
                        `;
                        docs.appendChild(div);
                    }
                }
                ['pestana2','pestana3','pestana4','pestana5','pestana9','pestana10'].forEach(pid=>{ const btn=document.getElementById(pid); if(btn&&btn.parentElement){ btn.parentElement.classList.add('active'); }});
                const modalEl = document.getElementById('addWorkerModal');
                if (modalEl) {
                    const titleEl = document.getElementById('addWorkerModalLabel');
                    const submitBtn = document.getElementById('submitBtn');
                    if (titleEl) titleEl.textContent = 'Editar Trabajador';
                    if (submitBtn) submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Guardar cambios';
                    (new bootstrap.Modal(modalEl)).show();
                }
            }
        })
        .catch(error => { console.error('Error al obtener datos:', error); });
    }




    function construirPaginacion(pagina, totalPages, limite, paginacionId) {
        const paginacion = $(`#${paginacionId}`);
        paginacion.empty();
        const createPageItem = (page, label = page) => `
            <li class="page-item ${pagina === page ? 'active' : ''}">
                <a class="page-link" href="#" onclick="cargarTrabajadores(${page}, ${limite})">${label}</a>
            </li>
        `;
        paginacion.append(`
            <li class="page-item ${pagina === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="cargarTrabajadores(${pagina - 1}, ${limite})">
                    <i class="fas fa-angle-left"></i><span>Prev</span>
                </a>
            </li>
        `);
        if (totalPages <= 7) {
            for (let i = 1; i <= totalPages; i++) {
                paginacion.append(createPageItem(i));
            }
        } else {
            if (pagina > 3) {
                paginacion.append(createPageItem(1));
                paginacion.append(`
                    <li class="page-item">
                        <input type="number" id="jumpToPage" class="form-control" placeholder="..." min="1" max="${totalPages}" style="width: 60px;" 
                            onkeydown="if(event.key === 'Enter') { let page = parseInt(this.value); if (page > totalPages) { page = totalPages; } cargarTrabajadores(page, ${limite}); }" 
                            oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                    </li>
                `);
            }
    
            const startPage = Math.max(1, pagina - 1);
            const endPage = Math.min(totalPages, pagina + 1);
            for (let i = startPage; i <= endPage; i++) {
                paginacion.append(createPageItem(i));
            }
    
            if (pagina < totalPages - 2) {
                paginacion.append(`
                    <li class="page-item">
                        <input type="number" id="jumpToPage" class="form-control" placeholder="..." min="1" max="${totalPages}" style="width: 60px;" 
                            onkeydown="if(event.key === 'Enter') { let page = parseInt(this.value); if (page > totalPages) { page = totalPages; } cargarTrabajadores(page, ${limite}); }" 
                            oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                    </li>
                    ${createPageItem(totalPages)}
                `);
            }
        }
        paginacion.append(`
            <li class="page-item ${pagina === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="cargarTrabajadores(${pagina + 1}, ${limite})"><span>Next</span><i class="fas fa-angle-right"></i></a>
            </li>
        `);
    }
    function construirHTMLDatos(workers) {
        const htmlDatos = document.getElementById('htmlDatos'); // Selecciona el contenedor
        let htmlContent = ''; // Variable para almacenar el HTML generado
    
        workers.forEach(worker => {
            const workerImg = worker.workerimg ? `/${worker.workerimg}` : '';
            const defaultImg = worker.workersex === 'F' 
                ? '/resource/images/foto-perfil-mujer.avif' 
                : '/resource/images/foto-perfil-hombre.avif';
    
            // Construir el HTML para cada trabajador
            htmlContent += `
                <div class="worker-card">
                    <div class="worker-image">
                        <img 
                            src="${workerImg}" 
                            onerror="this.src = '${defaultImg}';" 
                            loading="lazy" 
                            width="100" 
                            alt="${workerImg}">
                            <!--<p>${workerImg}</p>-->
                    </div>
                    <div class="worker-details">
                        <p><strong>Código:</strong> ${worker.workercode}</p>
                        <p><strong>Nombres:</strong> ${worker.fullname}</p>
                        <p><strong>CI:</strong> ${worker.workerdocnumber || '----'}</p>
                        <p><strong>Cargo:</strong> ${worker.workerrol || '----'}</p>
                        <p><strong>Fecha Ingreso:</strong> ${worker.workerdateinit || '----'}</p>
                        <p><strong>Sexo:</strong> ${worker.workersex || '----'}</p>
                    </div>
                    <div class="worker-actions">
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#workerview" data-bs-id="${worker.codeworker}">
                            <i class="fa-solid fa-magnifying-glass"></i> Ver
                        </button>
                        <button class="btn btn-sm btn-warning" onclick="getDataWorker('${worker.codeworker}'); return false;">
                            <i class="fa-solid fa-pen-to-square"></i> Editar
                        </button>
                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteWorkerModal" data-bs-id="${worker.codeworker}">
                            <i class="fa-solid fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            `;
        });
    
        // Insertar el HTML generado en el contenedor
        htmlDatos.innerHTML = htmlContent;
    }
        
   let workerviewModal = document.getElementById('workerview');

   workerviewModal.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;
        const targetAttr = button ? button.getAttribute('data-bs-target') : '';
        if (targetAttr && targetAttr !== '#workerview') {
            try { console.log('[LOG] workerview.show ignored', { targetAttr, rel: button }); } catch(_) {}
            event.preventDefault();
            return;
        }
        const codeworker = button ? button.getAttribute('data-bs-id') : '';
        try {
            const openModals = Array.from(document.querySelectorAll('.modal.show')).map(m => m.id);
            console.log('[LOG] workerview.show start', { codeworker, relatedTarget: !!button, openModals, activeElement: document.activeElement });
        } catch(e) {}
        if (typeof closeDeleteWorkerModal === 'function') closeDeleteWorkerModal();
        if (!codeworker) return;
        
        // Seleccionar los elementos del modal
        let inputName1 = workerviewModal.querySelector('.modal-body #worker_name1');
        let inputName2 = workerviewModal.querySelector('.modal-body #worker_name2');
        let inputLastName1 = workerviewModal.querySelector('.modal-body #worker_lastname1');
        let inputLastName2 = workerviewModal.querySelector('.modal-body #worker_lastname2');
        let inputDocId = workerviewModal.querySelector('.modal-body #worker-doc-id');
        let inputBirthday = workerviewModal.querySelector('.modal-body #worker-birthday');
        let inputGender = workerviewModal.querySelector('.modal-body #worker-gender');
        let inputBolNat = workerviewModal.querySelector('.modal-body #worker-bol-nat');
        let inputNationality = workerviewModal.querySelector('.modal-body #worker-nat');
        let inputCivilStatus = workerviewModal.querySelector('.modal-body #worker-civil-status');
        let inputLicense = workerviewModal.querySelector('.modal-body #worker-license');
        let inputMobile1 = workerviewModal.querySelector('.modal-body #worker-mobile');
        let inputMobile2 = workerviewModal.querySelector('.modal-body #worker-phone');
        let inputAddress = workerviewModal.querySelector('.modal-body #worker-address');
        let inputPhoto = workerviewModal.querySelector('.modal-body #worker-photo');
        let inputStartDate = workerviewModal.querySelector('.modal-body #start-date');
        let inputEndDate = workerviewModal.querySelector('.modal-body #end-date');
        let inputDepartment = workerviewModal.querySelector('.modal-body #department');
        let inputRole = workerviewModal.querySelector('.modal-body #role');
        let inputSection = workerviewModal.querySelector('.modal-body #section');
        let bankAccountsBody = workerviewModal.querySelector('.modal-body #bank-accounts-body');
        let familyBody = workerviewModal.querySelector('.modal-body #family-body');
        let turnwBody = workerviewModal.querySelector('.modal-body #turnw-body');
        let inputbanknum = workerviewModal.querySelector('.modal-body #worker-banknum');
        let inputnit = workerviewModal.querySelector('.modal-body #worker-nit');
    
        let url = '../routes/workers/workerview.php';
        let formData = new FormData();
        formData.append('codeworker', codeworker);
    
        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                return;
            }
    
            // Cargar datos personales
            inputName1.textContent = data.workername1 || 'N/A';
            inputName2.textContent = data.workername2 || 'N/A';
            inputLastName1.textContent = data.workerlastname1 || 'N/A';
            inputLastName2.textContent = data.workerlastname2 || 'N/A';
            inputDocId.textContent = data.workerdocnumber || 'N/A';
            inputBirthday.textContent = data.workerbirthdate || 'N/A';
            inputGender.textContent = data.workersex || 'N/A';
            inputBolNat.textContent = data.workercity || 'N/A';
            inputNationality.textContent = data.workernationality || 'N/A';
            inputCivilStatus.textContent = data.workercivilstatus || 'N/A';
            inputLicense.textContent = data.workerbanknum || 'N/A';
            inputMobile1.textContent = data.workerphone1 || 'N/A';
            inputMobile2.textContent = data.workerphone2 || 'N/A';
            inputAddress.textContent = data.workeraddress || 'N/A';
            (function(){
                const appBase = window.location.pathname.includes('/rrhh/') ? '/rrhh/' : '/';
                const defaultImg = appBase + (data.workersex === 'F' ? 'resource/images/foto-perfil-mujer.avif' : 'resource/images/foto-perfil-hombre.avif');
                const src = data.workerimg ? (appBase + String(data.workerimg).replace(/^\/?/, '')) : defaultImg;
                inputPhoto.src = src;
                inputPhoto.onerror = function(){ this.src = defaultImg; };
            })();
            inputStartDate.textContent = data.workerdateinit || 'N/A';
            inputEndDate.textContent = data.workerdateout || 'N/A';
            inputDepartment.textContent = data.workerarea_name || 'N/A';
            inputRole.textContent = data.workerrol_name || 'N/A';
            inputSection.textContent = data.workersection_name || 'N/A';
            inputbanknum.textContent = data.workerbanknum || 'N/A';
            inputnit.textContent = data.workerdateinit || 'N/A';
            
            cargarCombobox('department', '../routes/workers/getAreas.php', data.workerarea, 'department-text');
            cargarCombobox('role', '../routes/workers/getRoles.php', data.workerrol, 'role-text');
            cargarCombobox('section', '../routes/workers/getSections.php', data.workersection, 'section-text');
            
    
            // Cargar cuentas bancarias secundarias
            if (data.accounts && data.accounts.length > 0) {
                bankAccountsBody.innerHTML = ''; 
                data.accounts.forEach(account => {
                    let row = document.createElement('tr');
                    row.innerHTML = `
                        <td><b>Banco:</b> ${account.accountbank || 'N/A'}</td>
                        <td><b>Numero de cuenta:</b> ${account.accountnro || 'N/A'}</td>
                        <td class="text-end">
                            <i class="fas fa-edit edit-icon" onclick="editBankAccount(${account.id})" style="cursor:pointer"></i>
                            <i class="fas fa-trash delete-icon" onclick="deleteBankAccount(${account.id})" style="cursor:pointer; color:red; margin-left:10px"></i>
                        </td>
                    `;
                    bankAccountsBody.appendChild(row);
                });
            } else {
                bankAccountsBody.innerHTML = '<tr><td colspan="3">No hay cuentas bancarias registradas.</td></tr>';
            }
    
            // Cargar familiares
            if (data.family && data.family.length > 0) {
                familyBody.innerHTML = ''; 
                data.family.forEach(family => {
                    let row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${family.familyname || 'N/A'}</td>
                        <td>${family.familylastname || 'N/A'}</td>
                        <td>${family.familysex || 'N/A'}</td>
                        <td>${family.familyage || 'N/A'}</td>
                        <td>${family.familykin || 'N/A'}</td>
                        <td class="text-end">
                            <i class="fas fa-edit edit-icon" onclick="editFamilyMember(${family.id})" style="cursor:pointer"></i>
                            <i class="fas fa-trash delete-icon" onclick="deleteFamilyMember(${family.id})" style="cursor:pointer; color:red; margin-left:10px"></i>
                        </td>
                    `;
                    familyBody.appendChild(row);
                });
            } else {
                familyBody.innerHTML = '<tr><td colspan="6">No hay familiares registrados.</td></tr>';
            }
    
           // Cargar turnos
           if (data.turnw && data.turnw.length > 0) {
            turnwBody.innerHTML = '';
            
            fetch('../routes/workers/getTurns.php')
                .then(response => response.json())
                .then(allTurns => {
                    data.turnw.forEach(turnw => {
                        let row = document.createElement('tr');
                        row.innerHTML = `
                            <td>
                                <select class="form-control form-control-sm turn-select" 
                                        id="turn-select-${turnw.id}"
                                        disabled>
                                    ${allTurns.map(turn => 
                                        `<option value="${turn.id}" 
                                                 data-start="${turn.turn_start}" 
                                                 data-end="${turn.turn_end}"
                                                 ${turn.id == turnw.id ? 'selected' : ''}>
                                            ${turn.turn_name}
                                        </option>`
                                    ).join('')}
                                </select>
                            </td>
                            <td class="turn-start">${turnw.turn_start || 'N/A'}</td>
                            <td class="turn-end">${turnw.turn_end || 'N/A'}</td>
                            <td class="text-end">
                                <i class="fas fa-edit edit-icon" onclick="editWorkShift('${turnw.id}')" style="cursor:pointer"></i>
                                <i class="fas fa-trash delete-icon" onclick="deleteWorkShift('${turnw.id}')" style="cursor:pointer; color:red; margin-left:10px"></i>
                            </td>
                        `;
                        turnwBody.appendChild(row);
                    });
                });
        } else {
            turnwBody.innerHTML = '<tr><td colspan="4">No hay turnos registrados.</td></tr>';
        }
            
            // Cargar Documentos
            const documentBody = workerviewModal.querySelector('.modal-body #documentWorker-body');
            if (documentBody) {
                documentBody.innerHTML = '';
            }
            if (data.documents && data.documents.length > 0) {
                data.documents.forEach(doc => {
                    let row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${doc.gradoFormacion || 'N/A'}</td>
                        <td>${doc.titulo || 'N/A'}</td>
                        <td>${doc.urlCertificado ? `<a href="${doc.urlCertificado}" target="_blank">Ver certificado</a>` : 'N/A'}</td>
                        <td>${doc.descripcionCurso || 'N/A'}</td>
                        <td>${doc.fechaCursada || 'N/A'}</td>
                        <td class="text-end">
                            <i class="fas fa-edit edit-icon" onclick="editDocument(${doc.id})" style="cursor:pointer"></i>
                            <i class="fas fa-trash delete-icon" onclick="deleteDocument(${doc.id})" style="cursor:pointer; color:red; margin-left:10px"></i>
                        </td>
                    `;
                    if (documentBody) documentBody.appendChild(row);
                });
            } else {
                if (documentBody) documentBody.innerHTML = '<tr><td colspan="6">No hay documentos registrados.</td></tr>';
            }
            
        })
        .catch(err => console.error('Error al obtener los datos del trabajador:', err));
        setupWorkerModalEvents();
        
    });
    
    let currentlyEditingShift = null;

function editWorkShift(workShiftId) {
    // Si ya estamos editando este turno, no hacer nada
    if (currentlyEditingShift === workShiftId) return;
    
    // Si hay otro turno en edici��n, lo cerramos primero
    if (currentlyEditingShift) {
        const prevSelect = document.getElementById(`turn-select-${currentlyEditingShift}`);
        if (prevSelect) {
            prevSelect.disabled = true;
            updateTurnTimes(prevSelect, currentlyEditingShift);
        }
    }
    
    // Habilitar el select actual
    const selectElement = document.getElementById(`turn-select-${workShiftId}`);
    selectElement.disabled = false;
    selectElement.focus();
    
    // Marcar como turno actualmente en edici��n
    currentlyEditingShift = workShiftId;
    
    // Resaltar visualmente la fila en edici��n
    const row = selectElement.closest('tr');
    row.classList.add('editing-row');
    
    // Funci��n para finalizar la edici��n
    const finishEditing = () => {
        selectElement.disabled = true;
        updateTurnTimes(selectElement, workShiftId);
        saveWorkShiftChanges(workShiftId, selectElement.value);
        currentlyEditingShift = null;
        row.classList.remove('editing-row');
        
        // Remover los event listeners
        selectElement.removeEventListener('blur', finishEditing);
        document.removeEventListener('click', outsideClickListener);
    };
    
    // Evento para cuando pierda el foco
    selectElement.addEventListener('blur', finishEditing);
    
    // Evento para la tecla Enter
    selectElement.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            finishEditing();
        }
    });
    
    // Evento para detectar clics fuera del elemento
    const outsideClickListener = (event) => {
        if (!row.contains(event.target) && event.target !== selectElement) {
            finishEditing();
        }
    };
    
    setTimeout(() => {
        document.addEventListener('click', outsideClickListener);
    }, 0);
}

function updateTurnTimes(selectElement, workShiftId) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const startTime = formatTime(selectedOption.getAttribute('data-start'));
    const endTime = formatTime(selectedOption.getAttribute('data-end'));
    
    const row = selectElement.closest('tr');
    row.querySelector('.turn-start').textContent = startTime || 'N/A';
    row.querySelector('.turn-end').textContent = endTime || 'N/A';
}

// Funci��n para formatear la hora consistentemente
function formatTime(timeString) {
    if (!timeString) return null;
    
    // Eliminar segundos si existen
    if (timeString.includes(':') && timeString.split(':').length > 2) {
        const parts = timeString.split(':');
        return `${parts[0]}:${parts[1]}`;
    }
    return timeString;
}

function saveWorkShiftChanges(workShiftId, newTurnId) {
    // Aqu�� ir��a el c��digo para guardar los cambios en la base de datos
    console.log(`Guardando cambios: turno ${workShiftId} ahora es ${newTurnId}`);
    
    // Ejemplo de fetch:
    /*
    fetch('../routes/workers/updateWorkShift.php', {
        method: 'POST',
        body: JSON.stringify({
            workShiftId: workShiftId,
            newTurnId: newTurnId
        }),
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // Actualizar la interfaz si es necesario
        } else {
            console.error('Error al actualizar turno:', data.message);
        }
    });
    */
}
    function setupWorkerModalEvents() {
        document.querySelectorAll('.edit-icon').forEach(icon => {
            icon.style.display = 'none';
        });
        
        document.getElementById('edit-all-button')?.addEventListener('click', function() {
            document.querySelector('.cuerpoViewWorkers').classList.add('editing-mode');
            
            document.getElementById('save-button').style.display = 'inline-block';
            this.style.display = 'none';
        });

        document.querySelectorAll('.edit-icon').forEach(icon => {
            icon.addEventListener('click', function() {
                const field = this.getAttribute('data-field');
                const span = this.previousElementSibling;
                
                if (field === 'department' || field === 'role' || field === 'section') {
                    const select = document.getElementById(field);
                    select.disabled = !select.disabled;
                    this.classList.toggle('active');
                } 
                else {
                    const currentValue = span.textContent.trim();
                    const input = document.createElement('input');
                    input.type = 'text';
                    input.value = currentValue;
                    input.className = 'form-control form-control-sm';
                    
                    span.replaceWith(input);
                    input.focus();
                    
                    input.addEventListener('blur', function() {
                        span.textContent = this.value;
                        input.replaceWith(span);
                    });
                    
                    input.addEventListener('keypress', function(e) {
                        if (e.key === 'Enter') {
                            span.textContent = this.value;
                            input.replaceWith(span);
                        }
                    });
                }
            });
        });
        
        document.getElementById('save-button')?.addEventListener('click', function() {
            alert('Guardando cambios...');
            
            exitEditMode();
        });

        document.querySelectorAll('.delete-icon').forEach(icon => {
            icon.style.display = 'none';
        });
        
        const workerviewModal = document.getElementById('workerview');
        if (workerviewModal) {
            workerviewModal.addEventListener('hidden.bs.modal', function() {
                exitEditMode();
            });
        }
        
        function exitEditMode() {
            document.querySelector('.cuerpoViewWorkers')?.classList.remove('editing-mode');

            const editBtn = document.getElementById('edit-all-button');
            const saveBtn = document.getElementById('save-button');
            if (editBtn) editBtn.style.display = 'inline-block';
            if (saveBtn) saveBtn.style.display = 'none';
            
            document.querySelectorAll('.edit-icon, .delete-icon').forEach(icon => {
                icon.style.display = 'none';
            });
            
            ['department', 'role', 'section'].forEach(id => {
                const select = document.getElementById(id);
                if (select) select.disabled = true;
            });
            
            document.querySelectorAll('.edit-icon.active').forEach(icon => {
                icon.classList.remove('active');
            });
        }
    }
    
    // Funci��n para cargar comboboxes
    async function cargarCombobox(elementId, url, selectedValue = '') {
        const selectElement = document.getElementById(elementId);
        selectElement.innerHTML = '<option value="">Cargando...</option>';
        
        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            const data = await response.json();
            
            selectElement.innerHTML = '';
            
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = 'Seleccione...';
            selectElement.appendChild(defaultOption);
            
            let valueField, textField;
            if (url.includes('getAreas')) {
                valueField = 'codearea';
                textField = 'areaname';
            } else if (url.includes('getRoles')) {
                valueField = 'codeoccupation';
                textField = 'nameoccupation';
            } else if (url.includes('getSections')) {
                valueField = 'codesection';
                textField = 'namesection';
            }
            
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item[valueField];
                option.textContent = item[textField];
                
                if (selectedValue && option.value == selectedValue) {
                    option.selected = true;
                }
                
                selectElement.appendChild(option);
            });
            
            // Habilitar el select
            //selectElement.disabled = false;
            
        } catch (error) {
            console.error(`Error al cargar ${elementId}:`, error);
            selectElement.innerHTML = '<option value="">Error al cargar datos</option>';
        }
    }

    const deleteWorkerModal = document.getElementById('deleteWorkerModal');
    if (deleteWorkerModal) {
        deleteWorkerModal.addEventListener('show.bs.modal', function(event){
            try {
                const openBefore = Array.from(document.querySelectorAll('.modal.show')).map(m => m.id);
                console.log('[LOG] delete.show start', { openBefore, relatedTarget: !!event.relatedTarget });
            } catch(e) {}
            try {
                if (deleteWorkerModal.parentElement !== document.body) {
                    document.body.appendChild(deleteWorkerModal);
                    console.log('[LOG] delete.show movedToBody', { parent: deleteWorkerModal.parentElement && deleteWorkerModal.parentElement.tagName });
                }
            } catch(_) {}
            document.querySelectorAll('.modal.show').forEach(m => {
                const inst = bootstrap.Modal.getInstance(m);
                if (inst) inst.hide();
            });
            const button = event.relatedTarget;
            const codeId = button ? button.getAttribute('data-bs-id') : '';
            console.log('[LOG] delete.show codeId', { codeId });
            const hiddenInput = document.getElementById('codeworker_delete');
            if (hiddenInput) hiddenInput.value = codeId;
            const worker = lastWorkers.find(w => String(w.codeworker) === String(codeId));
            const activo = worker ? (worker.workerstate === 1 || worker.workerstate === '1' || worker.workerstate === true) : false;
            const estadoMensaje = document.getElementById('estadoMensaje');
            const btnConfirm = document.getElementById('btnConfirmInactivo');
            if (activo) {
                if (estadoMensaje) estadoMensaje.textContent = 'El trabajador está ACTIVO. Se marcará como INACTIVO.';
                if (btnConfirm) btnConfirm.disabled = false;
            } else {
                if (estadoMensaje) estadoMensaje.textContent = 'El trabajador ya está INACTIVO.';
                if (btnConfirm) btnConfirm.disabled = true;
            }
            try {
                const openAfter = Array.from(document.querySelectorAll('.modal.show')).map(m => m.id);
                const cs = window.getComputedStyle(deleteWorkerModal);
                const dlg = deleteWorkerModal.querySelector('.modal-dialog');
                const csDlg = dlg ? window.getComputedStyle(dlg) : null;
                const backdrop = document.querySelector('.modal-backdrop');
                const csBackdrop = backdrop ? window.getComputedStyle(backdrop) : null;
                console.log('[LOG] delete.show prepared', { openAfter, activeElement: document.activeElement, display: cs.display, visibility: cs.visibility, zIndex: cs.zIndex, dlgTransform: csDlg ? csDlg.transform : null, backdropOpacity: csBackdrop ? csBackdrop.opacity : null, backdropZ: csBackdrop ? csBackdrop.zIndex : null });
            } catch(e) {}
            setTimeout(() => {
                try {
                    const cs = window.getComputedStyle(deleteWorkerModal);
                    if (cs.display === 'none') {
                        console.warn('[LOG] delete.show fallback: forcing display block');
                        deleteWorkerModal.style.display = 'block';
                        deleteWorkerModal.classList.add('show');
                        deleteWorkerModal.setAttribute('aria-hidden', 'false');
                        const backdrop = document.querySelector('.modal-backdrop');
                        if (backdrop) backdrop.style.zIndex = '1060';
                        const csAfter = window.getComputedStyle(deleteWorkerModal);
                        console.log('[LOG] delete.show forced', { display: csAfter.display, visibility: csAfter.visibility, zIndex: csAfter.zIndex });
                        try {
                            const inst = bootstrap.Modal.getInstance(deleteWorkerModal) || new bootstrap.Modal(deleteWorkerModal, { backdrop: true, keyboard: true });
                            inst.show();
                        } catch(_) {}
                    }
                } catch(e) {}
            }, 50);
        });
        deleteWorkerModal.addEventListener('hidden.bs.modal', function(){
            const hiddenInput = document.getElementById('codeworker_delete');
            if (hiddenInput) hiddenInput.value = '';
            console.log('[LOG] delete.hidden');
            try {
                deleteWorkerModal.classList.remove('show');
                deleteWorkerModal.style.removeProperty('display');
                deleteWorkerModal.style.removeProperty('opacity');
                deleteWorkerModal.style.removeProperty('visibility');
                deleteWorkerModal.setAttribute('aria-hidden', 'true');
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) backdrop.style.removeProperty('z-index');
                console.log('[LOG] delete.hidden reset');
            } catch(_) {}
        });
        deleteWorkerModal.addEventListener('shown.bs.modal', function(){
            try {
                const cs = window.getComputedStyle(deleteWorkerModal);
                const dlg = deleteWorkerModal.querySelector('.modal-dialog');
                const csDlg = dlg ? window.getComputedStyle(dlg) : null;
                console.log('[LOG] delete.shown computed', { display: cs.display, visibility: cs.visibility, zIndex: cs.zIndex, dlgTransform: csDlg ? csDlg.transform : null });
                if (cs.display === 'none' || cs.visibility === 'hidden') {
                    deleteWorkerModal.style.display = 'block';
                    deleteWorkerModal.style.visibility = 'visible';
                }
                const inst = bootstrap.Modal.getInstance(deleteWorkerModal) || new bootstrap.Modal(deleteWorkerModal, { backdrop: true, keyboard: true });
                const closeBtns = deleteWorkerModal.querySelectorAll('[data-bs-dismiss="modal"], .btn-close');
                closeBtns.forEach(btn => {
                    btn.addEventListener('click', function(){
                        try { inst.hide(); } catch(_) {}
                    });
                });
            } catch(e) {}
        });

        document.addEventListener('click', function(e){
            const trigger = e.target.closest('[data-bs-target="#deleteWorkerModal"]');
            if (!trigger) return;
            const codeId = trigger.getAttribute('data-bs-id') || '';
            console.log('[LOG] delete.trigger click', { codeId, trigger });
        });
    }

    document.addEventListener('hidden.bs.modal', function(e){
        try {
            if (e.target.contains(document.activeElement)) {
                document.activeElement.blur();
            }
        } catch(_) {}
        e.target.setAttribute('inert', '');
    });

    document.addEventListener('show.bs.modal', function(e){
        e.target.removeAttribute('inert');
    });

    // Acción confirmar inactivar con Bootstrap modal
    (function(){
        const btnConfirm = document.getElementById('btnConfirmInactivo');
        if (btnConfirm) btnConfirm.addEventListener('click', function(){
            const disabled = this.disabled;
            if (disabled) {
                const estadoMensaje = document.getElementById('estadoMensaje');
                if (estadoMensaje) estadoMensaje.textContent = 'El trabajador ya está INACTIVO.';
            }
        });
    })();

    $(document).ready(() => cargarTrabajadores(currentPage, rowsPerPage));
</script>

<style>
.containerTable{
    width:100%;
    padding:0 15px;
    overflow-x:auto;
}

.containerBtnsSubPag{
    margin:0;
}
.btn-subpagina{
  padding: 7px 20px;
  border:none;
  font-size:1rem;
  border-radius: 7px 7px 0 0;
}
.btn-subpagina.pintado {
     background: rgb(163, 226, 38);
     box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
     color: white;
}



/*Estilo de la vista html de los trabajaores*/
#htmlDatos {
    display: flex;
    flex-wrap: wrap;
    justify-content:center;
    gap: 20px;
    padding: 20px;
}

.worker-card {
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    width: 250px;
    padding: 15px;
}

.worker-image img {
    border-radius: 8px;
    width: 220px;
    height: 220px;
    object-fit:cover;
}

.worker-details {
    margin-top: 10px;
}

.worker-details p {
    margin: 5px 0;
}

.worker-actions {
    margin-top: 15px;
    display: flex;
    gap: 10px;
}

.worker-actions .btn {
    flex: 1;
}
.text-end {
    text-align: end;
}

.edit-icon {
    color: #007bff;
    transition: all 0.3s;
}

.edit-icon:hover {
    color: #0056b3;
    transform: scale(1.2);
}

.delete-icon {
    transition: all 0.3s;
}

.delete-icon:hover {
    transform: scale(1.2);
}

/* Asegurar que las tablas tengan suficiente espacio */
.table {
    width: 100%;
}

.table td, .table th {
    vertical-align: middle;
}


#deleteWorkerModal { z-index: 1065; }
#deleteWorkerModal.show { display: block !important; visibility: visible !important; }
#deleteWorkerModal .modal-dialog { transform: translate(0, 0) !important; }
</style>