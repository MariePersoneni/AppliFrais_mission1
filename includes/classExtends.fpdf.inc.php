<?php
class PDF extends FPDF
{
    // En-tête
    function Header()
    {
        // Logo
        $this->Image('images/logo.jpg',80);
        // Saut de ligne
        $this->Ln(20);
    }
}