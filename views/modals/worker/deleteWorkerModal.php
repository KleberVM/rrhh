<div class="modal fade" id="deleteWorkerModal" tabindex="-1" aria-labelledby="deleteWorkerModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteWorkerModalLabel">Aviso</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        ¿Desea marcar este trabajador como inactivo?
        <p id="estadoMensaje" style="margin-top:8px;"></p>
      </div>
      <div class="modal-footer">
        <form action="routes/workers/deleteWorker.php" method="post">
          <input type="hidden" name="codeworker" id="codeworker_delete">
          <button type="submit" class="btn btn-danger" id="btnConfirmInactivo">Marcar Inactivo</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </form>
      </div>
    </div>
  </div>
</div>
