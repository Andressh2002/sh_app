<div class="col p-0">
    <div class="card overflow-hidden rounded-3" style="background: #DDDDDD;">
        <div class="admin-indicator-card-card-bg w-100 px-2 py-1">
            <h6 class="card-title fw-semibold"><?php echo $table['title']; ?></h6>
        </div>
        <div class="card-body pt-1 pb-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr class="bg-green text-white fw-semibold">
                            <?php foreach ($table['headers'] as $header): ?>
                            <th scope="col" class="align-middle"><?php echo $header; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody id="<?php echo $table['id']; ?>"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>