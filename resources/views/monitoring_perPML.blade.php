@extends('layouts.app')

@section('title', 'Monitoring PML')

@section('content')

<div class="container-fluid">

    <!-- <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            Monitoring Pencacah ({{ $pengawas->nama }})
        </h1>
    </div> -->

    <div class="card shadow mb-4">

        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Monitoring Pencacah (PML : {{ $pengawas->nama }})
            </h6>
        </div>
        
        <div class="card-body">
            <div class="container mb-2">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label>Tanggal Progres Harian</label>
                            <input type="text" id="tanggal" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-2">
                            <label>Tanggal Total Progres</label>
                            <input type="text" id="tanggalTerakhir" class="form-control" disabled>
                            <!-- atau isi lain -->
                        </div>
                    </div>

                    <div class="col-md-12">
                        <small class="text-muted d-block">
                            Data di bawah ini adalah Jumlah Assignment selain OPEN dan DRAFT
                        </small>
                    </div>

                    <div class="col-md-12">
                        <small class="text-muted d-block mb-2" id="keteranganPerhitungan">
                            Total Progress diperoleh dari data {{ $ketTanggalTerakhir }} dikurangi {{ $ketTanggalSebelumnya }}
                        </small>
                    </div>
                </div>

            </div>
            
            <div class="container">
                <div class="chart-area mb-3 mt-2">
                    <canvas id="progressChart"></canvas>
                </div>
            </div>
        </div>

    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Monitoring Progress Pencacah Seminggu Terakhir 
            </h6>
        </div>
        
        <div class="card-body">
            <div class="container">
                <div class="row mb-2">
                    Nama PPL
                </div> 
                <div class="row mb-2">
                <select class="form-control" id="id_ppl">
                    <!-- <option value=""></option> -->

                    @foreach ($pencacah as $p)
                        <option value="{{ $p->id }}">
                            {{ $p->nama }}
                        </option>
                    @endforeach
                </select>
                </div>   
                <!-- <div class="row mb-2">
                    Tanggal Progres (Jumlah Assignment selain OPEN dan DRAFT)
                </div> 
                <div class="row mb-2">
                    <input type="text" id="tanggal" class="form-control">
                </div>    -->

            
            </div>
            
            <div class="container">
                <div class="chart-area">
                    <canvas id="mingguanChart"></canvas>
                </div>
            </div>

        </div>

    </div>

</div>

@push('scripts')
<script>
    
    let progressChart;
    let mingguanChart;
    const colors = [
            "#90CAF9", // Light Blue
            "#A5D6A7", // Light Green
            "#FFCC80", // Light Orange
            "#CE93D8", // Light Purple
            "#80CBC4", // Teal
            "#FFE082", // Amber
            "#BCAAA4", // Brown
            "#B0BEC5"  // Blue Grey
        ];
    
    const fpTanggalTerakhir = flatpickr("#tanggalTerakhir", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "j F Y",
        defaultDate: "{{ $uploadTerakhir }}",
    });

    function loadChart(tanggal){
        // console.log(tanggal);
        $.get(
            "/monitoring/pml/{{ $pengawas->id }}/chart",
            {
                tanggal: tanggal
            },
            function(res){

                progressChart.data.datasets[0].data = res.progress;
                progressChart.data.datasets[1].data = res.total_progress;
                progressChart.data.datasets[2].data = res.sisa_target;

                progressChart.options.scales.y.suggestedMax =
                    Math.max(...res.total_progress, 0) + 5;
                
                fpTanggalTerakhir.setDate(res.tanggalTerakhir, true);


                progressChart.update();
                $('#keteranganPerhitungan').text(res.keterangan);
            }
        );
    }

    function loadChartMingguan(id_ppl){
        // console.log(tanggal);
        $.get(
            "/monitoring/pml/{{ $pengawas->id }}/chartMingguan",
            {
                id_ppl: id_ppl
            },
            function(res){

                mingguanChart.data.datasets[0].data = res.progress;

                mingguanChart.options.scales.y.suggestedMax =
                Math.max(...res.progress, 0) + 2;

                mingguanChart.update();
            }
        );
    }

    document.addEventListener("DOMContentLoaded", function () {
        // $('#id_ppl').select2({
        //     width: '100%'
        // });

        $('#id_ppl').on('change', function () {
            const idPpl = $(this).val();

            if (idPpl) {
                loadChartMingguan(idPpl);
            }
        });

        flatpickr("#tanggal", {
            dateFormat: "Y-m-d",      // Sent to server (e.g., 2026-10-14)
            altInput: true,          // Hides original input and creates a readable clone
            altFormat: "j F Y",
            defaultDate: "{{ $uploadSebelumnya }}",
            minDate: "2026-06-24",
            maxDate: "{{ $tanggalFlatpickr }}",

            onChange: function(selectedDates,dateStr){
                loadChart(dateStr);
                // console.log(dateStr);
            }
        });

        fpTanggalTerakhir;

        // flatpickr("#tanggalTerakhir", {
        //     dateFormat: "Y-m-d",      // Sent to server (e.g., 2026-10-14)
        //     altInput: true,          // Hides original input and creates a readable clone
        //     altFormat: "j F Y",
        //     defaultDate: "{{ $uploadTerakhir }}",
        // });


        const ctxProgress = document.getElementById('progressChart');
        const ctxMingguan = document.getElementById('mingguanChart');
        const chartData = JSON.parse('{!! json_encode($chart) !!}');
        const chartDataMingguan = JSON.parse('{!! json_encode($chartMingguan) !!}');
        // console.log(chartData);

        progressChart = new Chart(ctxProgress, {
            plugins:[ChartDataLabels],
            type: 'bar',

            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'Progress Harian',
                        data: chartData.progress,
                        backgroundColor: "#FFB74D",
                        // backgroundColor: chartData.progress.map((_, i) => colors[i % colors.length]),
                        // borderColor: chartData.progress.map((_, i) => colors[i % colors.length]),
                        // borderColor: "#C96A1B",
                        borderWidth: 0,
                        borderRadius: 5,
                        borderSkipped: false,
                        stack: 'harian',
                        // hoverBackgroundColor: "#F2A654",
                        // hoverBorderColor: "#B8671A"
                    },
                    {
                        label: 'Total Progress',
                        data: chartData.total_progress,
                        backgroundColor: "#66BB6A",
                        // backgroundColor: chartData.progress.map((_, i) => colors[i % colors.length]),
                        // borderColor: chartData.progress.map((_, i) => colors[i % colors.length]),
                        // borderColor: "#C96A1B",
                        borderWidth: 0,
                        borderRadius: 5,
                        borderSkipped: false,
                        stack: 'target',
                        // hoverBackgroundColor: "#F2A654",
                        // hoverBorderColor: "#B8671A"
                    },
                    {
                        label: 'Sisa Target',
                        data: chartData.sisa_target,
                        backgroundColor: "#E0E0E0",
                        borderSkipped: false,
                        borderRadius: {
                            topLeft: 5,
                            topRight: 5,
                            bottomLeft: 0,
                            bottomRight: 0,
                        },
                        stack: 'target',
                        hidden: true,
                    },
                ]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        stacked: true,
                        ticks: {
                            autoSkip: false,
                            maxRotation: window.innerWidth < 768 ? 45 : 0,
                            minRotation: window.innerWidth < 768 ? 45 : 0,
                            font: {
                                size: window.innerWidth < 768 ? 10 : 13
                            },
                            callback: function(value) {
                                const label = this.getLabelForValue(value);
                                const max = 12;

                                return label.length > max
                                    ? `${label.slice(0, max)}...`
                                    : label;
                            }
                        },
                        grid: {
                            display: false
                        },
                    },

                    y: {
                        stacked: true,
                        beginAtZero: true,
                        suggestedMax: Math.max(...chartData.total_progress) + 5,
                        // grace: '10%',
                        ticks: {
                            precision: 0
                        },
                        grid: {
                            color: "#EEEEEE"
                        }
                    }
                            },
                 plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        // labels: {
                        //     margin-bottom: 20
                        // }
                    },
                    datalabels: {
                        color: '#444',
                        anchor: 'end',
                        align: 'top',
                        offset: -4,
                        font: {
                            weight: 'bold',
                            size: 12
                        },
                        formatter: (value) => value
                    }
                },
            }
        });

        mingguanChart = new Chart(ctxMingguan, {
            plugins:[ChartDataLabels],
            type: 'line',

            data: {
                labels: chartDataMingguan.labels,
                datasets: [
                    {
                        data: chartDataMingguan.progress,
                        borderColor: "#E18A3A",
                        backgroundColor: "#E18A3A",
                        // borderColor: "#C96A1B",
                        borderWidth: 3,
                        // borderRadius: 5,
                        // borderSkipped: false,
                        tension: 0.3
                    },
                ]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        ticks: {
                            autoSkip: false,
                            maxRotation: window.innerWidth < 768 ? 45 : 0,
                            minRotation: window.innerWidth < 768 ? 45 : 0,
                            font: {
                                size: window.innerWidth < 768 ? 10 : 13
                            }
                        },
                        grid: {
                            display: false
                        }
                    },

                    y: {
                        beginAtZero: true,
                        suggestedMax: Math.max(...chartDataMingguan.progress) + 5,
                        ticks: {
                            precision: 0
                        },
                        grid: {
                            color: "#EEEEEE"
                        }
                    }
                            },
                plugins: {
                    legend: {
                        display: false
                    },
                    datalabels: {
                        color: '#444',
                        anchor: 'end',
                        align: 'top',
                        font: {
                            weight: 'bold',
                            size: 14
                        },
                        formatter: (value) => value
                    }
                },
            }
        });
    });
</script>
@endpush


@endsection