<?php

declare(strict_types=1);

namespace App\Application\Actions\Usuario;

use App\Application\Actions\Action;
use Psr\Log\LoggerInterface;
use App\Domain\Usuario\UsuarioRepository;

abstract class UsuarioAction extends Action
{
    protected UsuarioRepository $usuarioRepository;

    public function __construct(LoggerInterface $logger, UsuarioRepository $ur)
    {
        parent::__construct($logger);
        $this->usuarioRepository = $ur;
    }
}
