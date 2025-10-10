<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Monitor Koperasi Karyawan</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js for interactive charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc; /* slate-50 */
        }
        /* Custom styles for chart containers to maintain aspect ratio */
        .chart-container {
            position: relative;
            height: 350px;
            width: 100%;
        }
        .card {
            background-color: white;
            padding: 1.5rem; /* p-6 */
            border-radius: 1.5rem; /* rounded-3xl */
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.07), 0 4px 6px -4px rgb(0 0 0 / 0.1); /* shadow-lg custom */
            transition: all 0.3s ease-in-out;
        }
        .card:hover {
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1); /* shadow-xl */
            transform: translateY(-5px);
        }
        /* Styles for Gemini AI Modal */
        .modal-overlay {
            transition: opacity 0.3s ease;
        }
        .modal-container {
            transition: all 0.3s ease;
        }
        /* Spinner for loading state */
        .spinner {
            border-top-color: #3498db;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body class="text-slate-800">

    <div class="container mx-auto p-4 sm:p-6 lg:p-8">
        <!-- Header -->
        <header class="text-center mb-10">
            <h1 class="text-4xl font-extrabold bg-gradient-to-r from-blue-600 to-teal-400 bg-clip-text text-transparent pb-2">
                Koperasi Karyawan PT MKM
            </h1>
            <p class="text-slate-500 mt-1 text-lg">Dashboard Monitor</p>
        </header>

        <main class="grid grid-cols-1 gap-6">
            <!-- Top Section: Key Metrics Tables -->
            <div class="card">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-100 text-xs text-slate-700 uppercase">
                            <tr>
                                <th scope="col" class="px-6 py-3 rounded-l-lg font-semibold">Kondisi & Partisipasi</th>
                                <th scope="col" class="px-6 py-3 font-semibold">Deskripsi</th>
                                <th scope="col" class="px-6 py-3 text-center font-semibold" colspan="2">Simpan Pinjam</th>
                                <th scope="col" class="px-6 py-3 text-center font-semibold" colspan="2">Pertokoan</th>
                                <th scope="col" class="px-6 py-3 text-center rounded-r-lg font-semibold" colspan="2">Perdagangan & Jasa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-slate-100">
                                <td class="px-6 py-4 font-semibold text-green-700 bg-green-50 align-top rounded-l-xl">
                                    KONDISI KEUANGAN KOPERASI
                                    <span class="block text-xl font-bold">BAIK</span>
                                </td>
                                <td class="px-6 py-4 font-medium">Partisipasi Anggota</td>
                                <td class="px-6 py-4 text-center">100/305</td>
                                <td class="px-6 py-4 text-center font-semibold">30%</td>
                                <td class="px-6 py-4 text-center">100/305</td>
                                <td class="px-6 py-4 text-center font-semibold">30%</td>
                                <td class="px-6 py-4 text-center">100/305</td>
                                <td class="px-6 py-4 text-center font-semibold rounded-r-xl">30%</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 font-semibold text-green-700 bg-green-50 align-top rounded-l-xl">
                                    TINGKAT PARTISIPASI ANGGOTA
                                    <span class="block text-xl font-bold">BAIK</span>
                                </td>
                                <td class="px-6 py-4 font-medium">Transaksi Partisipasi Anggota</td>
                                <td class="px-6 py-4 text-center font-semibold" colspan="2">75%</td>
                                <td class="px-6 py-4 text-center font-semibold" colspan="2">68%</td>
                                <td class="px-6 py-4 text-center font-semibold rounded-r-xl" colspan="2">82%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Middle Section: Member Growth & Projections -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                 <!-- Penambahan Anggota -->
                <div class="card md:col-span-2 lg:col-span-2 grid grid-cols-3 gap-4 text-center items-center">
                    <div>
                        <p class="text-sm text-slate-500">Anggota Awal</p>
                        <p class="text-4xl font-bold">302</p>
                    </div>
                    <div class="relative">
                        <p class="text-sm text-slate-500">Penambahan</p>
                        <div class="flex items-center justify-center gap-2">
                             <p id="new-members" class="text-4xl font-bold">6</p>
                             <div class="flex flex-col">
                                <button onclick="updateMemberCount(1)" class="text-slate-500 hover:text-green-600 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l5 5a1 1 0 01-1.414 1.414L11 6.414V16a1 1 0 11-2 0V6.414L5.707 9.707a1 1 0 01-1.414-1.414l5-5A1 1 0 0110 3z" clip-rule="evenodd" /></svg>
                                </button>
                                <button onclick="updateMemberCount(-1)" class="text-slate-500 hover:text-red-600 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 17a1 1 0 01-.707-.293l-5-5a1 1 0 011.414-1.414L9 13.586V4a1 1 0 112 0v9.586l3.293-3.293a1 1 0 011.414 1.414l-5 5A1 1 0 0110 17z" clip-rule="evenodd" /></svg>
                                </button>
                             </div>
                        </div>
                        <p class="text-xs text-green-600 font-semibold">+0.20%</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Anggota Akhir</p>
                        <p id="total-members" class="text-4xl font-bold text-blue-600">308</p>
                    </div>
                </div>
                 <!-- Proyeksi Cards -->
                <div class="card text-center flex flex-col justify-center items-center">
                    <div class="bg-blue-100 text-blue-600 rounded-full p-3 mb-3"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg></div>
                    <p class="text-sm text-slate-500">Proyeksi SHU 2024</p>
                    <p class="text-2xl font-bold">Rp 1.2 M</p>
                </div>
                <div class="card text-center flex flex-col justify-center items-center">
                    <div class="bg-teal-100 text-teal-600 rounded-full p-3 mb-3"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg></div>
                    <p class="text-sm text-slate-500">Proyeksi SHU 2025</p>
                    <p class="text-2xl font-bold">Rp 1.5 M</p>
                </div>
                <div class="card text-center flex flex-col justify-center items-center">
                     <div class="bg-amber-100 text-amber-600 rounded-full p-3 mb-3"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg></div>
                    <p class="text-sm text-slate-500">Alokasi Pinjaman</p>
                    <p class="text-2xl font-bold">Rp 500 Jt</p>
                </div>
            </div>

            <!-- Bottom Section: Ratios and Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Left Column: Financial Ratios -->
                <div class="lg:col-span-1">
                    <div class="card h-full flex flex-col">
                        <h3 class="font-bold text-lg mb-4">Rasio Keuangan</h3>
                        <div class="space-y-5 flex-grow">
                            <!-- ... existing ratio items ... -->
                            <div class="flex items-start gap-4">
                                <div class="bg-green-100 text-green-600 rounded-lg p-2"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h6"></path><path d="M3 10h18"></path><path d="m16 20 2-2-2-2"></path><path d="M18 18h-5"></path></svg></div>
                                <div>
                                    <p class="font-semibold leading-tight">Net Profit Margin</p>
                                    <span id="npm-value" class="text-sm font-bold text-green-600">12.5%</span>
                                </div>
                            </div>
                             <div class="flex items-start gap-4">
                                <div class="bg-yellow-100 text-yellow-600 rounded-lg p-2"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16.5 13.4a1 1 0 0 0-1 0l-2.5 1.4a1 1 0 0 0 0 1.8l2.5 1.4a1 1 0 0 0 1 0l2.5-1.4a1 1 0 0 0 0-1.8l-2.5-1.4z"></path><path d="m20.5 17.8-2.5 1.4a1 1 0 0 1-1 0l-2.5-1.4a1 1 0 0 1 0-1.8l2.5-1.4a1 1 0 0 1 1 0l2.5 1.4a1 1 0 0 1 0 1.8z"></path><path d="M7.5 13.4a1 1 0 0 0-1 0l-2.5 1.4a1 1 0 0 0 0 1.8l2.5 1.4a1 1 0 0 0 1 0l2.5-1.4a1 1 0 0 0 0-1.8l-2.5-1.4z"></path><path d="m12 11 2.5-1.4a1 1 0 0 0 0-1.8L12 6.4a1 1 0 0 0-1 0L8.5 7.8a1 1 0 0 0 0 1.8L11 11a1 1 0 0 0 1 0z"></path></svg></div>
                                <div>
                                    <p class="font-semibold leading-tight">Debt Ratio</p>
                                    <span id="dr-value" class="text-sm font-bold text-yellow-600">45.2%</span>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="bg-blue-100 text-blue-600 rounded-lg p-2"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 5 4 4-10 10-4 1 1-4Z"></path><path d="M14.5 6.5 17.5 9.5"></path></svg></div>
                                <div>
                                    <p class="font-semibold leading-tight">Current Ratio</p>
                                    <span id="cr-value" class="text-sm font-bold text-blue-600">2.1x</span>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="bg-indigo-100 text-indigo-600 rounded-lg p-2"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"></path><path d="m19 9-5 5-4-4-3 3"></path></svg></div>
                                <div>
                                    <p class="font-semibold leading-tight">Return on Equity</p>
                                    <span id="roe-value" class="text-sm font-bold text-indigo-600">18.8%</span>
                                </div>
                            </div>
                        </div>
                        <!-- Gemini AI Button -->
                        <div class="mt-6">
                           <button id="get-analysis-btn" class="w-full bg-gradient-to-r from-slate-800 to-slate-700 text-white font-semibold py-3 px-4 rounded-xl hover:from-slate-900 hover:to-slate-800 transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-slate-400">
                                ✨ Dapatkan Analisis Keuangan AI
                           </button>
                        </div>
                    </div>
                </div>

                <!-- Middle Column: Main Line Chart -->
                <div class="lg:col-span-3 card">
                    <h3 class="font-bold text-lg mb-4">Analisis Sisa Hasil Usaha (SHU)</h3>
                    <div class="chart-container">
                        <canvas id="lineChart"></canvas>
                    </div>
                </div>

                <!-- Right Column: Asset Breakdown -->
                <div class="lg:col-span-4 card grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                    <div class="md:col-span-2">
                        <h3 class="font-bold text-lg mb-4">Komposisi Aset, Kewajiban, dan Ekuitas</h3>
                        <div class="space-y-4">
                            <!-- Aset -->
                            <div class="flex items-center gap-4">
                                <span class="w-28 text-sm font-medium text-right text-slate-600">ASET</span>
                                <div class="w-full bg-slate-200 rounded-full h-6 overflow-hidden">
                                    <div class="bg-blue-600 h-6" style="width: 100%"></div>
                                </div>
                                <span class="w-32 text-sm font-bold text-left">Rp 991.840.223</span>
                            </div>
                            <!-- Kewajiban -->
                             <div class="flex items-center gap-4">
                                <span class="w-28 text-sm font-medium text-right text-slate-600">KEWAJIBAN</span>
                                <div class="w-full bg-slate-200 rounded-full h-6 overflow-hidden">
                                    <div class="bg-violet-500 h-6" style="width: 10%"></div>
                                </div>
                                <span class="w-32 text-sm font-bold text-left">Rp 99.168.839</span>
                            </div>
                            <!-- Ekuitas -->
                             <div class="flex items-center gap-4">
                                <span class="w-28 text-sm font-medium text-right text-slate-600">EKUITAS</span>
                                <div class="w-full bg-slate-200 rounded-full h-6 overflow-hidden">
                                    <div class="bg-teal-500 h-6" style="width: 90%"></div>
                                </div>
                                <span class="w-32 text-sm font-bold text-left">Rp 892.671.384</span>
                            </div>
                        </div>
                    </div>
                     <div class="md:col-span-1 chart-container" style="height: 250px;">
                        <canvas id="doughnutChart"></canvas>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Gemini AI Modal -->
    <div id="ai-modal" class="fixed inset-0 z-50 items-center justify-center hidden">
        <div id="modal-overlay" class="absolute inset-0 bg-black bg-opacity-60 modal-overlay"></div>
        <div id="modal-container" class="relative bg-white w-full max-w-2xl mx-4 p-8 rounded-3xl shadow-2xl transform scale-95 modal-container">
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-teal-400 bg-clip-text text-transparent">
                    Analisis Keuangan Berbasis AI
                </h2>
                <button id="close-modal-btn" class="text-slate-400 hover:text-slate-800 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <!-- Modal Content -->
            <div id="modal-content" class="text-slate-600 leading-relaxed">
                <!-- Loading State -->
                <div id="loading-state" class="flex flex-col items-center justify-center text-center">
                    <div class="spinner w-12 h-12 rounded-full border-4 border-slate-200 mb-4"></div>
                    <p class="font-semibold">Harap tunggu, AI sedang menganalisis data keuangan...</p>
                    <p class="text-sm text-slate-500">Proses ini mungkin memakan waktu beberapa detik.</p>
                </div>
                 <!-- Result State -->
                <div id="result-state" class="hidden prose max-w-none">
                    <!-- AI generated content will be injected here -->
                </div>
            </div>
        </div>
    </div>


    <script>
        // --- Interactive Member Count ---
        const initialMembers = 302;
        const newMembersEl = document.getElementById('new-members');
        const totalMembersEl = document.getElementById('total-members');

        function updateMemberCount(change) {
            let currentNew = parseInt(newMembersEl.innerText);
            currentNew += change;
            if (currentNew < 0) currentNew = 0; // Prevent negative members

            newMembersEl.innerText = currentNew;
            totalMembersEl.innerText = initialMembers + currentNew;
        }


        // --- Chart.js Initializations ---
        document.addEventListener('DOMContentLoaded', () => {
            // --- Line Chart: Sisa Hasil Usaha ---
            const ctxLine = document.getElementById('lineChart').getContext('2d');
            new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
                    datasets: [{
                        label: 'Pendapatan',
                        data: [50, 52, 55, 54, 56, 58, 57, 59, 56, 53, 50, 48],
                        borderColor: 'rgb(79, 70, 229)', // indigo-600
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: 'rgb(79, 70, 229)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgb(79, 70, 229)',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    }, {
                        label: 'HPP',
                        data: [15, 16, 14, 17, 18, 19, 20, 18, 17, 16, 15, 14],
                        borderColor: 'rgb(249, 115, 22)', // orange-500
                        backgroundColor: 'rgba(249, 115, 22, 0.1)',
                        tension: 0.4,
                        fill: true,
                    }, {
                        label: 'Pengeluaran',
                        data: [10, 11, 12, 11, 13, 12, 14, 13, 12, 11, 10, 9],
                        borderColor: 'rgb(239, 68, 68)', // red-500
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        fill: true,
                    }]
                },
                options: {
                    // ... existing chart options ...
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#e5e7eb' // gray-200
                            },
                            ticks: {
                                callback: function(value) {
                                    return (value).toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, notation: 'compact' });
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                             labels: {
                                usePointStyle: true,
                                boxWidth: 8
                            }
                        },
                        tooltip: {
                             backgroundColor: '#1e293b', // slate-800
                             titleFont: {
                                size: 14,
                                weight: 'bold'
                             },
                             bodyFont: {
                                size: 12
                             },
                             padding: 12,
                             cornerRadius: 8,
                             boxPadding: 4,
                             callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += context.parsed.y.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' });
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });

            // --- Doughnut Chart: Komposisi Aset ---
            const ctxDoughnut = document.getElementById('doughnutChart').getContext('2d');
            new Chart(ctxDoughnut, {
                type: 'doughnut',
                // ... existing doughnut chart data and options ...
                data: {
                    labels: ['Ekuitas', 'Kewajiban'],
                    datasets: [{
                        label: 'Komposisi',
                        data: [892671384, 99168839],
                        backgroundColor: [
                            'rgb(20, 184, 166)', // teal-500
                            'rgb(139, 92, 246)', // violet-500
                        ],
                        hoverOffset: 8,
                        borderWidth: 0,
                    }]
                },
                 options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 8
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        label += context.parsed.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' });
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });

            // --- Gemini AI Modal Logic ---
            const modal = document.getElementById('ai-modal');
            const overlay = document.getElementById('modal-overlay');
            const container = document.getElementById('modal-container');
            const openBtn = document.getElementById('get-analysis-btn');
            const closeBtn = document.getElementById('close-modal-btn');
            const loadingState = document.getElementById('loading-state');
            const resultState = document.getElementById('result-state');

            const openModal = () => {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => {
                    overlay.classList.remove('opacity-0');
                    container.classList.remove('scale-95');
                }, 10);
            };

            const closeModal = () => {
                overlay.classList.add('opacity-0');
                container.classList.add('scale-95');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }, 300);
            };

            openBtn.addEventListener('click', () => {
                openModal();
                getFinancialAnalysis();
            });
            closeBtn.addEventListener('click', closeModal);
            overlay.addEventListener('click', closeModal);

            async function getFinancialAnalysis() {
                // Reset state
                loadingState.style.display = 'flex';
                resultState.style.display = 'none';
                resultState.innerHTML = '';

                // Get data from the dashboard
                const npm = document.getElementById('npm-value').innerText;
                const dr = document.getElementById('dr-value').innerText;
                const cr = document.getElementById('cr-value').innerText;
                const roe = document.getElementById('roe-value').innerText;

                const userQuery = `
                Anda adalah seorang analis keuangan ahli untuk koperasi di Indonesia. Berdasarkan data berikut:
                - Net Profit Margin: ${npm}
                - Debt Ratio: ${dr}
                - Current Ratio: ${cr}
                - Return on Equity: ${roe}

                Tolong berikan:
                1.  **Analisis Singkat:** Satu paragraf ringkas mengenai kesehatan keuangan koperasi saat ini.
                2.  **Rekomendasi:** Tiga poin rekomendasi konkret yang bisa dijalankan untuk perbaikan atau optimalisasi.

                Gunakan format HTML sederhana (paragraf, heading, dan list) untuk respons Anda.
                `;

                const apiKey = ""; // API key will be automatically provided by the environment
                const apiUrl = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-preview-05-20:generateContent?key=${apiKey}`;

                const payload = {
                    contents: [{ parts: [{ text: userQuery }] }],
                };

                try {
                    const response = await fetch(apiUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });

                    if (!response.ok) {
                        throw new Error(`API Error: ${response.status} ${response.statusText}`);
                    }

                    const result = await response.json();
                    const analysisText = result.candidates?.[0]?.content?.parts?.[0]?.text;

                    if (analysisText) {
                        resultState.innerHTML = analysisText.replace(/\n/g, '<br>'); // Basic formatting
                    } else {
                        resultState.innerHTML = '<p>Maaf, AI tidak dapat memberikan analisis saat ini. Silakan coba lagi nanti.</p>';
                    }
                } catch (error) {
                    console.error("Error fetching AI analysis:", error);
                    resultState.innerHTML = `<p class="text-red-600">Terjadi kesalahan saat menghubungi layanan AI. Periksa konsol untuk detail.</p>`;
                } finally {
                    loadingState.style.display = 'none';
                    resultState.style.display = 'block';
                }
            }
        });
    </script>
    <script>
        async function hydrateDashboard() {
          try {
            const res = await fetch('/api/coop-dashboard');
            const data = await res.json();

            // Header table
            // Partisipasi “100/305” dan “30%”
            const h = data.header.partisipasi;

            // Simpan Pinjam
            document.querySelectorAll('td').forEach((td) => {
              // Sederhana: cari cell sesuai urutan static—atau beri ID di HTML untuk presisi
            });
            // Lebih presisi: beri ID pada tiap cell di HTML, misal:
            // <td id="sp-count">100/305</td> <td id="sp-rate">30%</td> dst.

            const fmtPct = (v) => `${(v ?? 0).toFixed(0)}%`;
            const fmtRatio = (v) => v != null ? (typeof v === 'number' ? v : parseFloat(v)).toFixed(1) : '-';
            const rp = (n) => (n ?? 0).toLocaleString('id-ID', { style:'currency', currency:'IDR', maximumFractionDigits:0 });

            // Jika kamu tambahkan ID di HTML:
            // document.getElementById('sp-count').textContent = `${h.simpan_pinjam.active}/${h.simpan_pinjam.total}`;
            // document.getElementById('sp-rate').textContent  = fmtPct(h.simpan_pinjam.rate);
            // document.getElementById('toko-count').textContent = `${h.pertokoan.active}/${h.pertokoan.total}`;
            // document.getElementById('toko-rate').textContent  = fmtPct(h.pertokoan.rate);
            // document.getElementById('jasa-count').textContent = `${h.perdagangan_jasa.active}/${h.perdagangan_jasa.total}`;
            // document.getElementById('jasa-rate').textContent  = fmtPct(h.perdagangan_jasa.rate);

            // Anggota
            document.getElementById('new-members').textContent = data.members.new;
            document.getElementById('total-members').textContent = data.members.final;
            // (kalau mau tampilkan growth_pct juga, tinggal buat elemen baru)

            // Proyeksi kartu
            // beri ID pada elemen nominal agar mudah update, contoh:
            // <p id="proj-shu-2024" class="text-2xl font-bold">Rp 1.2 M</p>
            // document.getElementById('proj-shu-2024').textContent = rp(data.projections.shu_2024);
            // document.getElementById('proj-shu-2025').textContent = rp(data.projections.shu_2025);
            // document.getElementById('loan-alloc').textContent   = rp(data.projections.loan_allocation);

            // Rasio (elemen sudah ada ID-nya)
            document.getElementById('npm-value').textContent = (data.ratios.npm_percent ?? 0) + '%';
            document.getElementById('dr-value').textContent  = (data.ratios.dr_percent  ?? 0) + '%';
            document.getElementById('cr-value').textContent  = fmtRatio(data.ratios.cr_times) + 'x';
            document.getElementById('roe-value').textContent = (data.ratios.roe_percent ?? 0) + '%';

            // Komposisi (progress bar + donut dataset)
            // Progress bar: update label nominal (beri ID di HTML)
            // <span id="val-assets"  class="w-32 ...">Rp ...</span> dst.
            // document.getElementById('val-assets').textContent  = rp(data.composition.assets);
            // document.getElementById('val-liabs').textContent   = rp(data.composition.liabilities);
            // document.getElementById('val-equity').textContent  = rp(data.composition.equity);

            // Update lebar bar (liabilities/assets * 100, equity/assets * 100)
            const percentL = Math.min(100, Math.round((data.composition.liabilities / data.composition.assets) * 100));
            const percentE = Math.min(100, Math.round((data.composition.equity / data.composition.assets) * 100));
            // document.querySelector('#bar-liab').style.width = percentL+'%';
            // document.querySelector('#bar-eq').style.width   = percentE+'%';

            // Update chart data (kalau mau sinkronisasi penuh)
            // window.lineChartRef.data.datasets[0].data = data.line.revenue.map(n => n/1); // dsb
            // window.lineChartRef.update();
            // window.doughnutRef.data.datasets[0].data = [data.composition.equity, data.composition.liabilities];
            // window.doughnutRef.update();

          } catch (e) {
            console.error('Hydrate error:', e);
          }
        }

        document.addEventListener('DOMContentLoaded', hydrateDashboard);
        </script>

</body>
</html>

