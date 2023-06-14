<?php
include 'db_connect.php'; // Supondo que o arquivo de conexão com o banco de dados esteja incluído

function getSupplier()
{
    include 'db_connect.php';
    $suppliers = array();
    $qry = $conn->query("SELECT * FROM supplier_list ORDER BY name ASC");
    while ($row = $qry->fetch_assoc()) {
        $suppliers[$row['id']] = ucwords($row['name']);
    }
    return $suppliers;
}

function getProduct()
{
    include 'db_connect.php';
    $products = array();
    $qry = $conn->query("SELECT * FROM product_list ORDER BY name ASC");
    while ($row = $qry->fetch_assoc()) {
        $products[$row['id']] = array('name' => ucwords($row['name']), 'sku' => $row['sku'], 'price' => $row['price']);
    }
    return $products;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("SELECT p.* FROM receiving_list p WHERE p.id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        foreach ($row as $k => $v) {
            $$k = $v;
        }

        if ($from_order == 1) {
            $stmt = $conn->prepare("SELECT p.*, s.name AS supplier FROM purchase_order_list p INNER JOIN supplier_list s ON p.supplier_id = s.id WHERE p.id = ?");
            $stmt->bind_param("s", $form_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                foreach ($row as $k => $v) {
                    if ($k == 'id') {
                        $k = 'po_id';
                    }
                    if (!isset($$k)) {
                        $$k = $v;
                    }
                }
            }
        } else {
            $stmt = $conn->prepare("SELECT b.*, s.name AS supplier, p.po_code FROM back_order_list b INNER JOIN supplier_list s ON b.supplier_id = s.id INNER JOIN purchase_order_list p ON b.po_id = p.id WHERE b.id = ?");
            $stmt->bind_param("s", $_GET['bo_id']);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                foreach ($row as $k => $v) {
                    if ($k == 'id') {
                        $k = 'bo_id';
                    }
                    if (!isset($$k)) {
                        $$k = $v;
                    }
                }
            }
        }
    }
}

if (isset($_GET['po_id'])) {
    $po_id = $_GET['po_id'];

    $stmt = $conn->prepare("SELECT p.*, s.name AS supplier FROM purchase_order_list p INNER JOIN supplier_list s ON p.supplier_id = s.id WHERE p.id = ?");
    $stmt->bind_param("s", $po_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        foreach ($row as $k => $v) {
            if ($k == 'id') {
                $k = 'po_id';
            }
            $$k = $v;
        }
    }
}

if (isset($_GET['bo_id'])) {
    $bo_id = $_GET['bo_id'];

    $stmt = $conn->prepare("SELECT b.*, s.name AS supplier, p.po_code FROM back_order_list b INNER JOIN supplier_list s ON b.supplier_id = s.id INNER JOIN purchase_order_list p ON b.po_id = p.id WHERE b.id = ?");
    $stmt->bind_param("s", $bo_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        foreach ($row as $k => $v) {
            if ($k == 'id') {
                $k = 'bo_id';
            }
            $$k = $v;
        }
    }
}
?>

<style>
    select[readonly].select2-hidden-accessible+.select2-container {
        pointer-events: none;
        touch-action: none;
        background: #eee;
        box-shadow: none;
    }

    select[readonly].select2-hidden-accessible+.select2-container .select2-selection {
        background: #eee;
        box-shadow: none;
    }
</style>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h4 class="card-title">
            <?php echo !isset($id) ? "Receber Pedido de " . $po_code : 'Atualizar Pedido Recebido' ?>
        </h4>
    </div>
    <div class="card-body">
        <form action="" id="receive-form">
            <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
            <input type="hidden" name="from_order" value="<?php echo isset($bo_id) ? 2 : 1 ?>">
            <input type="hidden" name="form_id" value="<?php echo isset($po_id) ? $po_id : '' ?>">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="supplier_id">Fornecedor</label>
                        <select class="form-control select2" id="supplier_id" name="supplier_id" <?php echo isset($supplier_id) ? 'readonly' : '' ?>>
                            <?php
                            if (isset($supplier_id)) {
                                echo "<option value='{$supplier_id}'>{$supplier}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="receive_date">Data de Recebimento</label>
                        <input type="text" class="form-control datepicker" id="receive_date" name="receive_date"
                            value="<?php echo isset($receive_date) ? date("m/d/Y", strtotime($receive_date)) : date("m/d/Y") ?>"
                            readonly>
                    </div>
                </div>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Preço Unitário</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($id)) {
                        $stmt = $conn->prepare("SELECT * FROM receiving_item_list WHERE receiving_id = ?");
                        $stmt->bind_param("s", $id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>{$row['product_name']}</td>";
                                echo "<td>{$row['quantity']}</td>";
                                echo "<td>{$row['price']}</td>";
                                echo "<td>" . number_format($row['quantity'] * $row['price'], 2) . "</td>";
                                echo "</tr>";
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>

            <div class="form-group">
                <label for="remarks">Observações</label>
                <textarea class="form-control" id="remarks"
                    name="remarks"><?php echo isset($remarks) ? $remarks : '' ?></textarea>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <?php echo !isset($id) ? 'Receber Pedido' : 'Atualizar Pedido Recebido' ?>
                </button>
                <a href="purchase_order.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.select2').select2();

        $('.datepicker').datepicker({
            format: 'mm/dd/yyyy',
            autoclose: true,
        });

        $('#receive-form').submit(function (e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            var method = form.attr('method');
            var data = form.serialize();

            $.ajax({
                url: url,
                type: method,
                data: data,
                success: function (response) {
                    // Tratar a resposta da requisição
                },
                error: function (xhr, status, error) {
                    console.log(error);
                }
            });
        });
    });
</script>