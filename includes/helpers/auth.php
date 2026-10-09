<?php
/**
 * Control de acceso del panel de administración.
 */

declare(strict_types=1);

function admin_logueado(): bool
{
    return !empty($_SESSION['admin']);
}

function requerir_admin(): void
{
    if (!admin_logueado()) {
        redirect('admin/login.php');
    }
}
