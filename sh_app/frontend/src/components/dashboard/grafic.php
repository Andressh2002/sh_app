<div class="col-12 p-0 dashboard-shadow">

    <div class="dashboard-chart-card">

        <div class="dashboard-section-header">

            <h6>
                <?php echo $grafic['title']; ?>
            </h6>

        </div>

        <div class="dashboard-chart-body">

            <canvas
                id="<?php echo $grafic['id']; ?>"
                class="dashboard-chart-canvas"
            ></canvas>

        </div>

    </div>

</div>