@extends('layouts.Admin.App')

@section('content')
<!-- Page Header -->
<div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
    <div>
        <h1 class="text-2xl lg:text-3xl font-bold text-white mb-1 tracking-tight">System <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#0ea5e9] to-[#818cf8]">Overview</span></h1>
        <p class="text-sm text-slate-400">Neural network is currently operating at optimal capacity.</p>
    </div>
    <div class="">
        <button onclick="window.location.reload()" class="bg-[#38bdf8]/10 text-[#38bdf8] border border-[#38bdf8]/30 hover:bg-[#38bdf8] hover:text-white transition-all px-4 py-2 rounded-xl text-sm font-medium flex items-center shadow-[0_0_15px_rgba(56,189,248,0.15)] hover:shadow-[0_0_20px_rgba(56,189,248,0.4)]">
            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Refresh Nodes
        </button>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <!-- Stat 1: Total Patients -->
    <div class="glass-panel p-6 rounded-2xl relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300 border border-white/5">
        <div class="absolute -right-10 -top-10 w-32 h-32 bg-[#0ea5e9] opacity-10 rounded-full blur-2xl group-hover:opacity-20 transition-opacity"></div>
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-sm font-medium text-slate-400">Total Patients</p>
                <h3 class="text-3xl font-bold text-white mt-1">{{ number_format($totalPatients) }}</h3>
            </div>
            <div class="p-3 rounded-xl bg-[#0ea5e9]/10 text-[#0ea5e9] border border-[#0ea5e9]/20">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
        </div>
        <div class="flex items-center text-sm">
            <span class="{{ $patientGrowth >= 0 ? 'text-emerald-400' : 'text-red-400' }} flex items-center font-medium">
                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $patientGrowth >= 0 ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6' }}" />
                </svg>
                {{ abs($patientGrowth) }}%
            </span>
            <span class="text-slate-500 ml-2 shadow-text">vs prev {{ $period }}</span>
        </div>
    </div>

    <!-- Stat 2: Est. Revenue -->
    <div class="glass-panel p-6 rounded-2xl relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300 border border-white/5">
        <div class="absolute -right-10 -top-10 w-32 h-32 bg-[#818cf8] opacity-10 rounded-full blur-2xl group-hover:opacity-20 transition-opacity"></div>
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-sm font-medium text-slate-400">Est. MRR</p>
                <h3 class="text-3xl font-bold text-white mt-1">${{ number_format($totalRev, 2) }}</h3>
            </div>
            <div class="p-3 rounded-xl bg-[#818cf8]/10 text-[#818cf8] border border-[#818cf8]/20">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>
        <div class="flex items-center text-sm">
            <span class="{{ $revGrowth >= 0 ? 'text-emerald-400' : 'text-red-400' }} flex items-center font-medium">
                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $revGrowth >= 0 ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6' }}" />
                </svg>
                {{ abs($revGrowth) }}%
            </span>
            <span class="text-slate-500 ml-2 shadow-text">vs prev {{ $period }}</span>
        </div>
    </div>

    <!-- Stat 3: Active Nodes -->
    <div class="glass-panel p-6 rounded-2xl relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300 border border-white/5">
        <div class="absolute -right-10 -top-10 w-32 h-32 bg-[#38bdf8] opacity-10 rounded-full blur-2xl group-hover:opacity-20 transition-opacity"></div>
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-sm font-medium text-slate-400">Medical Nodes</p>
                <h3 class="text-3xl font-bold text-white mt-1">{{ number_format($totalNodes) }}</h3>
            </div>
            <div class="p-3 rounded-xl bg-[#38bdf8]/10 text-[#38bdf8] border border-[#38bdf8]/20">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
            </div>
        </div>
        <div class="flex items-center text-sm">
            <span class="{{ $nodeGrowth >= 0 ? 'text-emerald-400' : 'text-red-400' }} flex items-center font-medium">
                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $nodeGrowth >= 0 ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6' }}" />
                </svg>
                {{ abs($nodeGrowth) }}%
            </span>
            <span class="text-slate-500 ml-2 shadow-text">vs prev {{ $period }}</span>
        </div>
    </div>

    <!-- Stat 4: AI Scans -->
    <div class="glass-panel p-6 rounded-2xl relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300 border border-white/5">
        <div class="absolute -right-10 -top-10 w-32 h-32 bg-[#f472b6] opacity-10 rounded-full blur-2xl group-hover:opacity-20 transition-opacity"></div>
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-sm font-medium text-slate-400">AI Scans Processed</p>
                <h3 class="text-3xl font-bold text-white mt-1">{{ number_format($totalScans) }}</h3>
            </div>
            <div class="p-3 rounded-xl bg-[#f472b6]/10 text-[#f472b6] border border-[#f472b6]/20">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
            </div>
        </div>
        <div class="flex items-center text-sm">
            <span class="{{ $scanGrowth >= 0 ? 'text-emerald-400' : 'text-red-400' }} flex items-center font-medium">
                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $scanGrowth >= 0 ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6' }}" />
                </svg>
                {{ abs($scanGrowth) }}%
            </span>
            <span class="text-slate-500 ml-2 shadow-text">vs prev {{ $period }}</span>
        </div>
    </div>
</div>

<!-- Main Content Area with Chart -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    
    <!-- Large Chart Section -->
    <div class="lg:col-span-2 glass-panel p-6 rounded-2xl border border-white/5 flex flex-col">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h2 class="text-lg font-semibold text-white">AI Traffic & Telemetry</h2>
            
            <div class="flex bg-slate-800/50 rounded-lg p-1 border border-white/5 shadow-inner">
                <a href="?period=daily" class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all {{ $period == 'daily' ? 'bg-[#38bdf8] text-white shadow-[0_0_10px_rgba(56,189,248,0.3)]' : 'text-slate-400 hover:text-white' }}">Daily</a>
                <a href="?period=weekly" class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all {{ $period == 'weekly' ? 'bg-[#38bdf8] text-white shadow-[0_0_10px_rgba(56,189,248,0.3)]' : 'text-slate-400 hover:text-white' }}">Weekly</a>
                <a href="?period=monthly" class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all {{ $period == 'monthly' ? 'bg-[#38bdf8] text-white shadow-[0_0_10px_rgba(56,189,248,0.3)]' : 'text-slate-400 hover:text-white' }}">Monthly</a>
                <a href="?period=yearly" class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all {{ $period == 'yearly' ? 'bg-[#38bdf8] text-white shadow-[0_0_10px_rgba(56,189,248,0.3)]' : 'text-slate-400 hover:text-white' }}">Yearly</a>
                <a href="?period=all_time" class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all {{ $period == 'all_time' ? 'bg-[#38bdf8] text-white shadow-[0_0_10px_rgba(56,189,248,0.3)]' : 'text-slate-400 hover:text-white' }}">All Time</a>
            </div>
        </div>
        
        <div class="flex-1 w-full rounded-xl bg-slate-900/50 border border-white/5 flex items-center justify-center relative overflow-hidden group p-4 min-h-[300px]">
            <canvas id="trafficChart"></canvas>
        </div>
    </div>

    <!-- Recent Activity List -->
    <div class="glass-panel p-6 rounded-2xl border border-white/5 flex flex-col h-[400px] lg:h-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold text-white">System Events</h2>
            <span class="px-2 py-1 bg-brand-500/20 text-brand-400 text-[10px] uppercase font-bold tracking-wider rounded-md border border-brand-500/20">Live</span>
        </div>
        
        <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar">
            <ul class="space-y-6">
                @forelse($activityLogs as $log)
                <li class="relative pl-6 before:content-[''] before:absolute before:left-1.5 before:top-2 before:bottom-[-20px] before:w-[1px] before:bg-white/10 last:before:hidden">
                    @if($log['type'] === 'node')
                        <div class="absolute left-0 top-1.5 w-3 h-3 rounded-full bg-[#38bdf8] border-[3px] border-[#0f172a] shadow-[0_0_8px_#38bdf8]"></div>
                    @elseif($log['type'] === 'patient')
                        <div class="absolute left-0 top-1.5 w-3 h-3 rounded-full bg-emerald-400 border-[3px] border-[#0f172a] shadow-[0_0_8px_#34d399]"></div>
                    @else
                        <div class="absolute left-0 top-1.5 w-3 h-3 rounded-full bg-[#f472b6] border-[3px] border-[#0f172a] shadow-[0_0_8px_#f472b6]"></div>
                    @endif
                    
                    <div class="text-sm text-slate-300 leading-relaxed">
                        {!! $log['msg'] !!}
                    </div>
                    <div class="text-[11px] text-slate-500 mt-1 font-medium tracking-wide">{{ $log['time'] }}</div>
                </li>
                @empty
                <div class="text-sm text-slate-500 py-8 text-center italic font-light">No events registered yet.</div>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('trafficChart').getContext('2d');
    
    let gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(56, 189, 248, 0.5)'); 
    gradient.addColorStop(1, 'rgba(56, 189, 248, 0.05)');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'AI Scans Operations',
                data: {!! json_encode($chartData) !!},
                borderColor: '#38bdf8',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#0f172a',
                pointBorderColor: '#38bdf8',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.95)',
                    titleColor: '#fff',
                    bodyColor: '#cbd5e1',
                    titleFont: { family: 'Plus Jakarta Sans', size: 13 },
                    bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1,
                    padding: 12,
                    displayColors: false,
                    cornerRadius: 8
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255,255,255,0.05)', drawBorder: false },
                    ticks: { color: '#64748b', font: {family: 'Plus Jakarta Sans'}, precision: 0, padding: 10 }
                },
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: { color: '#64748b', font: {family: 'Plus Jakarta Sans'}, maxTicksLimit: 8, padding: 10 }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
        }
    });
});
</script>
@endsection
