<?php if ($_settings->chk_flashdata('success')): ?>
	<script>
		alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success')
	</script>
<?php endif; ?>
<style>
	.img-avatar {
		width: 45px;
		height: 45px;
		object-fit: cover;
		object-position: center center;
		border-radius: 100%;
	}
</style>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Lista de Usuários do Sistema</h3>
		<div class="card-tools">
			<a href="?page=user/manage_user" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span> Criar
				Novo</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<div class="container-fluid">
				<table class="table table-hover table-striped">
					<!-- <colgroup>
					<col width="5%">
					<col width="10%">
					<col width="20%">
					<col width="20%">
					<col width="15%">
					<col width="15%">
					<col width="10%">
				</colgroup> -->
					<thead>
						<tr>
							<th>#</th>
							<th>Avatar</th>
							<th>Nome</th>
							<th>Nome de Usuário</th>
							<th>Tipo de Usuário</th>
							<th>Ação</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$i = 1;
						$qry = $conn->query("SELECT *,concat(firstname,' ',lastname) as name from `usuarios` where id != '1' order by concat(firstname,' ',lastname) asc ");
						while ($row = $qry->fetch_assoc()):
							?>
							<tr>
								<td class="text-center">
									<?php echo $i++; ?>
								</td>
								<td class="text-center"><img src="<?php echo validate_image($row['avatar']) ?>"
										class="img-avatar img-thumbnail p-0 border-2" alt="user_avatar"></td>
								<td>
									<?php echo ucwords($row['name']) ?>
								</td>
								<td>
									<p class="m-0 truncate-1">
										<?php echo $row['username'] ?>
									</p>
								</td>
								<td>
									<p class="m-0">
										<?php echo ($row['type'] == 1) ? "Administrador" : "Equipe" ?>
									</p>
								</td>
								<td align="center">
									<button type="button"
										class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon"
										data-toggle="dropdown">
										Ação
										<span class="sr-only">Toggle Dropdown</span>
									</button>
									<div class="dropdown-menu" role="menu">
										<a class="dropdown-item"
											href="?page=user/manage_user&id=<?php echo $row['id'] ?>"><span
												class="fa fa-edit text-primary"></span> Editar</a>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item delete_data" href="javascript:void(0)"
											data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span>
											Excluir</a>
									</div>
								</td>
							</tr>
						<?php endwhile; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function () {
		$('.delete_data').click(function () {
			_conf("Tem certeza de que deseja excluir este usuário permanentemente?", "delete_user", [$(this).attr('data-id')])
		})
		$('.table td,.table th').addClass('py-1 px-2 align-middle')
		$('.table').dataTable();
	})
	function delete_user($id) {
		start_loader();
		$.ajax({
			url: _base_url_ + "classes/Users.php?f=delete",
			method: "POST",
			data: { id: $id },
			dataType: "json",
			error: err => {
				console.log(err)
				alert_toast("Ocorreu um erro.", 'error');
				end_loader();
			},
			success: function (resp) {
				if (typeof resp == 'object' && resp.status == 'success') {
					location.reload();
				} else {
					alert_toast("Ocorreu um erro.", 'error');
					end_loader();
				}
			}
		})
	}
</script>