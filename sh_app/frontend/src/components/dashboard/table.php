<div class="col-12 col-xl p-0 dashboard-shadow">

    <div class="dashboard-table-card">

        <div class="dashboard-section-header">

            <h6>
                <?php echo $table['title']; ?>
            </h6>

        </div>

        <div class="dashboard-table-body">

            <div class="table-responsive">

                <table
                    class="
                        table
                        dashboard-table
                        table-hover
                    "
                >

                    <thead>

                        <tr>

                            <?php
                            foreach(
                                $table['headers']
                                as $header
                            ):
                            ?>

                            <th>
                                <?php echo $header; ?>
                            </th>

                            <?php endforeach; ?>

                        </tr>

                    </thead>

                    <tbody id="<?php echo $table['id']; ?>"></tbody>

                </table>

            </div>

        </div>

    </div>

</div>