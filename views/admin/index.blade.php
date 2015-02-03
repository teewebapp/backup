@extends('admin::layouts.main')

@section('content')
    <table class="table table-hover table-page-list">
        <tbody>
            <tr>
                <th>Arquivo</th>
                <th>Tamanho</th>
                <th>Opções</th>
            </tr>

            @if($listBackup)
                @foreach($listBackup as $backupFile)
                    <tr>
                        <td>
                            {{{ $backupFile->getName() }}}
                        </td>
                        <td>
                            {{ round($backupFile->getSize() / 1024 / 1024, 3) . 'mb' }}
                        </td>
                        <td>
                            {{ HTML::downloadButton('Download', route("admin.backup.download", ['id'=>$backupFile->getId(), 'storageId'=>$backupFile->getStorage()->getId()])) }}

                            {{ HTML::deleteButton('Remover', route("admin.backup.destroy", ['id'=>$backupFile->getId(), 'storageId'=>$backupFile->getStorage()->getId()])) }}
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="4">
                        Nenhum backup encontrado
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <a class="btn btn-primary" href="{{ route("admin.backup.create") }}">
        Efetuar Backup
    </a>
@stop