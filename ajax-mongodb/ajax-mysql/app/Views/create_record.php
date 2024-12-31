<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Records</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Manage Records</h1>

    <!-- Display Records -->
    <div id="records">
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $record): ?>
                    <tr id="record-<?= $record['id'] ?>">
                        <td><?= $record['id'] ?></td>
                        <td><?= $record['name'] ?></td>
                        <td><?= $record['email'] ?></td>
                        <td>
                            <button class="btn-update" data-id="<?= $record['id'] ?>">Update</button>
                            <button class="btn-delete" data-id="<?= $record['id'] ?>">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Create Record Form -->
    <form id="createForm">
        <input type="text" name="name" placeholder="Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <button type="submit">Create Record</button>
    </form>

    <script>
        const baseUrl = '<?= base_url() ?>';

        $('#createForm').on('submit', function (e) {
            e.preventDefault();
            const formData = $(this).serialize();

            $.post(`${baseUrl}/record/create`, formData, function (response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message);
                }
            });
        });

        $(document).on('click', '.btn-delete', function () {
            const id = $(this).data('id');

            $.post(`${baseUrl}/record/delete/${id}`, function (response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message);
                }
            });
        });

        $(document).on('click', '.btn-update', function () {
            const id = $(this).data('id');
            const name = prompt('Enter new name:');
            const email = prompt('Enter new email:');

            if (name && email) {
                $.post(`${baseUrl}/record/update/${id}`, { name, email }, function (response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                });
            }
        });
    </script>
</body>
</html>
