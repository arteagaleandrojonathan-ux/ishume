<?php
class DashboardController {

    public function Dashboard() {
        $vista = 'dashboard';
        require_once __DIR__ . '/../views/PanelPrincipal.php';
    }
}