@extends('layouts.app')

@section('content')
<div style="padding: 20px;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="font-size: 28px; font-weight: bold;">📊 Histórico de Relatórios</h1>
        <a href="{{ route('relatorio.index') }}"
           style="background: #3B82F6; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: bold;">
            ➕ Gerar Novo Relatório
        </a>
    </div>

    @if(session('success'))
        <div style="background: #D1FAE5; border-left: 4px solid #10B981; color: #065F46; padding: 16px; margin-bottom: 20px; border-radius: 8px;">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #FEE2E2; border-left: 4px solid #EF4444; color: #991B1B; padding: 16px; margin-bottom: 20px; border-radius: 8px;">
            ❌ {{ session('error') }}
        </div>
    @endif

    {{-- Cards de Estatísticas --}}
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;">

        <div style="background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%); padding: 24px; border-radius: 12px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">Total de Relatórios</div>
            <div style="font-size: 36px; font-weight: bold;">{{ $relatorios->total() }}</div>
        </div>

        <div style="background: linear-gradient(135deg, #10B981 0%, #059669 100%); padding: 24px; border-radius: 12px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">PDFs Gerados</div>
            <div style="font-size: 36px; font-weight: bold;">{{ \App\Models\Relatorio::where('formato', 'pdf')->count() }}</div>
        </div>

        <div style="background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); padding: 24px; border-radius: 12px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">CSVs Gerados</div>
            <div style="font-size: 36px; font-weight: bold;">{{ \App\Models\Relatorio::where('formato', 'csv')->count() }}</div>
        </div>

        <div style="background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%); padding: 24px; border-radius: 12px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">Usuários Ativos</div>
            <div style="font-size: 36px; font-weight: bold;">{{ \App\Models\Relatorio::distinct('user_id')->count('user_id') }}</div>
        </div>

    </div>

    {{-- Filtros --}}
    <div style="background: white; padding: 24px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h3 style="font-size: 18px; font-weight: bold; margin-bottom: 16px;">🔍 Filtros</h3>

        <form method="GET" action="{{ route('admin.relatorios.historico') }}">
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 1fr; gap: 12px; margin-bottom: 16px;">

                <input type="text" name="busca" value="{{ request('busca') }}"
                       placeholder="Buscar por nome ou e-mail..."
                       style="border: 1px solid #D1D5DB; border-radius: 8px; padding: 10px; width: 100%;">

                <select name="formato" style="border: 1px solid #D1D5DB; border-radius: 8px; padding: 10px; width: 100%;">
                    <option value="">Todos formatos</option>
                    <option value="pdf" {{ request('formato') == 'pdf' ? 'selected' : '' }}>PDF</option>
                    <option value="csv" {{ request('formato') == 'csv' ? 'selected' : '' }}>CSV</option>
                </select>

                <select name="tipo_usuario" style="border: 1px solid #D1D5DB; border-radius: 8px; padding: 10px; width: 100%;">
                    <option value="">Todos usuários</option>
                    <option value="admin" {{ request('tipo_usuario') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="professor" {{ request('tipo_usuario') == 'professor' ? 'selected' : '' }}>Professor</option>
                    <option value="visitante" {{ request('tipo_usuario') == 'visitante' ? 'selected' : '' }}>Visitante</option>
                </select>

                <input type="date" name="data_inicio" value="{{ request('data_inicio') }}"
                       style="border: 1px solid #D1D5DB; border-radius: 8px; padding: 10px; width: 100%;">

                <input type="date" name="data_fim" value="{{ request('data_fim') }}"
                       style="border: 1px solid #D1D5DB; border-radius: 8px; padding: 10px; width: 100%;">
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="submit" style="background: #3B82F6; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">
                    🔍 Filtrar
                </button>
                @if(request()->hasAny(['busca', 'formato', 'tipo_usuario', 'data_inicio', 'data_fim']))
                    <a href="{{ route('admin.relatorios.historico') }}"
                       style="background: #6B7280; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold;">
                        ✖️ Limpar
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabela --}}
    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #F3F4F6;">
                <tr>
                    <th style="padding: 16px; text-align: left; font-weight: bold; font-size: 12px; color: #374151; text-transform: uppercase;">DATA</th>
                    <th style="padding: 16px; text-align: left; font-weight: bold; font-size: 12px; color: #374151; text-transform: uppercase;">USUÁRIO</th>
                    <th style="padding: 16px; text-align: left; font-weight: bold; font-size: 12px; color: #374151; text-transform: uppercase;">TIPO</th>
                    <th style="padding: 16px; text-align: left; font-weight: bold; font-size: 12px; color: #374151; text-transform: uppercase;">FORMATO</th>
                    <th style="padding: 16px; text-align: right; font-weight: bold; font-size: 12px; color: #374151; text-transform: uppercase;">AÇÕES</th>
                </tr>
            </thead>
            <tbody>
                @forelse($relatorios as $relatorio)
                    <tr style="border-bottom: 1px solid #E5E7EB;">
                        <td style="padding: 16px; font-size: 14px;">{{ $relatorio->created_at->format('d/m/Y H:i') }}</td>
                        <td style="padding: 16px;">
                            <div style="font-weight: 600; font-size: 14px;">{{ $relatorio->user->name }}</div>
                            <div style="font-size: 12px; color: #6B7280;">{{ ucfirst($relatorio->user->role ?? '—') }}</div>
                        </td>
                        <td style="padding: 16px;">
                            <span style="background: #DBEAFE; color: #1E40AF; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                {{ ucfirst($relatorio->tipo ?? '—') }}
                            </span>
                        </td>
                        <td style="padding: 16px;">
                            @if(strtoupper($relatorio->formato) == 'PDF')
                                <span style="background: #FEE2E2; color: #991B1B; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">PDF</span>
                            @else
                                <span style="background: #D1FAE5; color: #065F46; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">CSV</span>
                            @endif
                        </td>
                        <td style="padding: 16px; text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                <a href="{{ route('admin.relatorios.download', $relatorio->id) }}"
                                   style="background: #3B82F6; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">
                                    📥 Download
                                </a>
                                <form action="{{ route('admin.relatorios.excluir', $relatorio->id) }}" method="POST"
                                      onsubmit="return confirm('Tem certeza que deseja excluir?')" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: #EF4444; color: white; padding: 6px 12px; border: none; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600;">
                                        🗑️ Excluir
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 48px; text-align: center; color: #9CA3AF;">
                            <div style="font-size: 48px; margin-bottom: 16px;">📭</div>
                            <div style="font-size: 18px; font-weight: 600; color: #6B7280; margin-bottom: 8px;">Nenhum relatório encontrado</div>
                            <div style="font-size: 14px;">Tente ajustar os filtros ou gere um novo relatório.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginação --}}
    @if($relatorios->hasPages())
        <div style="margin-top: 24px;">
            {{ $relatorios->links() }}
        </div>
    @endif

</div>
@endsection

