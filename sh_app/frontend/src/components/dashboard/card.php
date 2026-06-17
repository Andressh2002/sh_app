<div class="col p-0 dashboard-shadow">

    <div class="dashboard-kpi-card">

        <div class="dashboard-kpi-header">

            <h6
                class="dashboard-kpi-title"
                id="<?php echo $cardDash['idTitle']; ?>"
            >
                <?php echo $cardDash['title']; ?>
            </h6>

        </div>

        <div class="dashboard-kpi-body">

            <h2
                class="dashboard-kpi-value"
                id="<?php echo $cardDash['idCant']; ?>"
            >
                <?php echo $cardDash['cant']; ?>
            </h2>

            <div
                class="dashboard-kpi-percent"
            >
                <?php echo $cardDash['help'] ?? ''; ?>
            </div>

        </div>

    </div>

</div>