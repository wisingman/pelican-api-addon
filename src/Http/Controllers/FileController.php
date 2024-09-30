<?php

namespace Wising\PelicanApiAddon\Http\Controllers;

use Illuminate\Http\Response;
use App\Models\Server;
use App\Models\AuditLog;
use App\Http\JsonResponse;
use App\Repositories\Wings\DaemonFileRepository;
use App\Http\Controllers\Api\Client\ClientApiController;
use Wising\PelicanApiAddon\Http\Requests\WriteFileContentRequest;

class FileController extends ClientApiController
{
    /**
     * @var \App\Repositories\Wings\DaemonFileRepository
     */
    private $fileRepository;

    /**
     * FileController constructor.
     */
    public function __construct(
        DaemonFileRepository $fileRepository
    ) {
        parent::__construct();

        $this->fileRepository = $fileRepository;
    }

    /**
     * Writes the contents of the specified file to the server.
     *
     * @throws \App\Exceptions\Http\Connection\DaemonConnectionException
     */
    public function __invoke(WriteFileContentRequest $request, Server $server): JsonResponse
    {
        $server->audit(AuditLog::SERVER__FILESYSTEM_WRITE, function (AuditLog $audit, Server $server) use ($request) {
            $audit->subaction = 'write_content';
            $audit->metadata = ['file' => $request->get('file')];

            $this->fileRepository
                ->setServer($server)
                ->putContent($request->get('file'), $request->get('content') ?: '');
        });

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

}
