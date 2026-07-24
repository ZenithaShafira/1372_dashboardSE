@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard ({{ $ketUploadTerakhir }})</h1>
        <!-- <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
            class="fas fa-download fa-sm text-white-50"></i> Generate Report</a> -->
    </div>

    <!-- Content Row -->
    <!-- <div class="row">
        <div class="col-xl-12 col-md-12 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Seluruh Progress</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $allProgress }} assignment
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-12 col-md-12 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col-1 text-center">
                            <i class="fas fa-medal fa-2x mb-2"></i>
                        </div>
                        <div class="col-8">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Seluruh Progress
                            </div>
                            <div class="h4 font-weight-bold text-primary mb-0">
                                Total 
                            </div>
                            <div class="h4 font-weight-bold text-primary mb-0">
                                Progress
                            </div>
                            <small class="text-muted">
                                assignment
                            </small>
                        </div>

                        <div class="col-auto">
                            <div class="h2 font-weight-bold text-primary mb-0">
                                {{ number_format($allProgress) }}
                            </div>
                            <small class="text-muted">
                                assignment
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-7 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Jumlah Progress per Prelist
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                        50%
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar"
                                            style="width: 50%" aria-valuenow="50" aria-valuemin="0"aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="h5 mb-0  font-weight-bold text-gray-800">
                                        50%
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                                <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

    <!-- Content Row -->
    <div class="row">

        <!-- Top 10 Atas Chart  -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
            
                <!-- Card Header -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Top 10 Total Progress Tertinggi</h6>
                </div>

                <!-- Card Body -->
                
                <div class="card-body">

                    {{-- PODIUM --}}
                    <!-- <div class="row align-items-end text-center mb-4 mt-2 align-items-center">

                        {{-- Juara 2 --}}
                        <div class="col-4">
                            <div class="card shadow podium podium-2 bg-secondary text-white">
                                <div class="card-body">
                                    <i class="fas fa-medal fa-2x mb-2"></i>
                                    <h5>{{ $top10tinggi[1]->nama }}</h5>
                                    <h3>{{ number_format($top10tinggi[1]->total_progress) }}</h3>
                                </div>
                            </div>
                        </div>

                        {{-- Juara 1 --}}
                        <div class="col-4">
                            <div class="card shadow-lg podium podium-1 bg-warning text-white">
                                <div class="card-body">
                                    <i class="fas fa-trophy fa-3x mb-2"></i>
                                    <h4>{{ $top10tinggi[0]->nama }}</h4>
                                    <h2>{{ number_format($top10tinggi[0]->total_progress) }}</h2>
                                </div>
                            </div>
                        </div>

                        {{-- Juara 3 --}}
                        <div class="col-4">
                            <div class="card shadow podium podium-3 bg-info text-white">
                                <div class="card-body">
                                    <i class="fas fa-award fa-2x mb-2"></i>
                                    <h5>{{ $top10tinggi[2]->nama }}</h5>
                                    <h3>{{ number_format($top10tinggi[2]->total_progress) }}</h3>
                                </div>
                            </div>
                        </div>

                    </div> -->

                    @php
                        $max = $top10tinggi->max('total_progress');
                    @endphp

                    {{-- Ranking 4-10 --}}
                    @foreach($top10tinggi as $p)

                    <div class="row align-items-center py-2 {{ $loop->odd ? 'bg-light-custom' : '' }}">

                        <div class="col-2 font-weight-bold text-gray-800 text-center">
                            {{ $loop->iteration}}
                        </div>

                        <div class="col-8 text-gray-800">
                            {{ $p->nama }}
                        </div>

                        <!-- <div class="col-5">

                            <div class="progress" style="height:18px;">
                                <div class="progress-bar bg-primary"
                                    style="width: {{ ($p->total_progress/$max)*100 }}%">
                                </div>
                            </div>

                        </div> -->

                        <div class="col-2 text-right font-weight-bold text-gray-800 text-center">
                            {{ number_format($p->total_progress) }}
                        </div>

                    </div>

                    @endforeach

                </div>
            </div>
        </div>

        <!-- Top 10 Rendah Chart  -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
            
                <!-- Card Header -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Top 10 Total Progress Terendah</h6>
                </div>

                <!-- Card Body -->
                
                <div class="card-body">
                    @php
                        $max = $top10rendah->max('total_progress');
                    @endphp

                    {{-- Ranking 4-10 --}}
                    @foreach($top10rendah as $p)

                    <div class="row align-items-center py-2 {{ $loop->odd ? 'bg-light-custom' : '' }}">

                        <div class="col-2 font-weight-bold text-gray-800 text-center">
                            {{ 61 + $loop->index }}
                        </div>        

                        <div class="col-8 text-gray-800">
                            {{ $p->nama }}
                        </div>

                        <!-- <div class="col-5">

                            <div class="progress" style="height:18px;">
                                <div class="progress-bar bg-primary"
                                    style="width: {{ ($p->total_progress/$max)*100 }}%">
                                </div>
                            </div>

                        </div> -->

                        <div class="col-2 text-right font-weight-bold text-gray-800 text-center">
                            {{ number_format($p->total_progress) }}
                        </div>

                    </div>

                    @endforeach

                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>

</script>
@endpush

@endsection