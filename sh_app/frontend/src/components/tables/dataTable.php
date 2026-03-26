<div class="table-responsive">
    <table class="table table-hover table-striped mb-0">
        <thead>
            <tr>
                <?php foreach ($headers as $header): ?>
                <th scope="col" class="align-middle"><?php echo $header; ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody id="data-container"></tbody>
    </table>
</div>