<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">Advanced Patient History</h1>
        <div class="flex space-x-3">
            <button onclick="exportPatientData()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md">
                <i class="fas fa-download mr-2"></i>Export Data
            </button>
            <button onclick="openAdvancedSearch()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                <i class="fas fa-search-plus mr-2"></i>Advanced Search
            </button>
        </div>
    </div>

    <!-- Advanced Search Panel -->
    <div id="advancedSearchPanel" class="bg-white rounded-lg shadow-lg p-6 hidden">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Smart Patient Search</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search Term</label>
                <input type="text" id="smartSearchInput" placeholder="Name, phone, or ID..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Age Range</label>
                <select id="ageRangeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">All Ages</option>
                    <option value="0-18">0-18 years</option>
                    <option value="19-35">19-35 years</option>
                    <option value="36-50">36-50 years</option>
                    <option value="51-65">51-65 years</option>
                    <option value="66-120">65+ years</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Last Visit</label>
                <select id="lastVisitFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">Any Time</option>
                    <option value="7">Last 7 days</option>
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 3 months</option>
                    <option value="365">Last year</option>
                </select>
            </div>
        </div>
        <div class="mt-4 flex justify-end space-x-2">
            <button onclick="performSmartSearch()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                <i class="fas fa-search mr-2"></i>Search
            </button>
            <button onclick="closeAdvancedSearch()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md">
                Cancel
            </button>
        </div>
    </div>

    <!-- Search Results -->
    <div id="searchResults" class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Search Results</h3>
        </div>
        <div id="searchResultsContent" class="p-6">
            <div class="text-center text-gray-500">Use the search above to find patients</div>
        </div>
    </div>

    <!-- Patient Analytics Dashboard -->
    <div id="patientAnalytics" class="hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Health Trends Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Health Trends</h3>
                <div class="space-y-4">
                    <!-- Blood Pressure Trend -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Blood Pressure Trend</h4>
                        <div class="relative h-32 bg-gray-50 rounded-lg p-4">
                            <canvas id="bpTrendChart" width="400" height="120"></canvas>
                        </div>
                    </div>
                    
                    <!-- Lab Results Trend -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Key Lab Results</h4>
                        <div class="relative h-32 bg-gray-50 rounded-lg p-4">
                            <canvas id="labTrendChart" width="400" height="120"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clinical Decision Support -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Clinical Alerts</h3>
                <div id="clinicalAlerts" class="space-y-3">
                    <!-- Alerts will be populated here -->
                </div>
            </div>
        </div>

        <!-- Comprehensive Patient Timeline -->
        <div class="bg-white rounded-lg shadow mt-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Medical Timeline</h3>
            </div>
            <div class="p-6">
                <div id="medicalTimeline" class="relative">
                    <!-- Timeline will be populated here -->
                </div>
            </div>
        </div>

        <!-- Patient Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Visits</p>
                        <p id="totalVisits" class="text-3xl font-bold">-</p>
                    </div>
                    <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                        <i class="fas fa-calendar-check text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Lab Tests</p>
                        <p id="totalLabTests" class="text-3xl font-bold">-</p>
                    </div>
                    <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                        <i class="fas fa-flask text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Medications</p>
                        <p id="totalMedications" class="text-3xl font-bold">-</p>
                    </div>
                    <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                        <i class="fas fa-pills text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-medium">Risk Score</p>
                        <p id="riskScore" class="text-3xl font-bold">-</p>
                    </div>
                    <div class="bg-orange-400 bg-opacity-30 rounded-full p-3">
                        <i class="fas fa-exclamation-triangle text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentPatientId = null;

function openAdvancedSearch() {
    document.getElementById('advancedSearchPanel').classList.remove('hidden');
    document.getElementById('smartSearchInput').focus();
}

function closeAdvancedSearch() {
    document.getElementById('advancedSearchPanel').classList.add('hidden');
}

function performSmartSearch() {
    const searchTerm = document.getElementById('smartSearchInput').value;
    const ageRange = document.getElementById('ageRangeFilter').value;
    const lastVisit = document.getElementById('lastVisitFilter').value;

    if (searchTerm.length < 2) {
        showToast('Please enter at least 2 characters to search', 'warning');
        return;
    }

    // Show loading state
    document.getElementById('searchResultsContent').innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i>
            <p class="text-gray-500">Searching patients...</p>
        </div>
    `;

    // Simulate API call (replace with actual AJAX call)
    setTimeout(() => {
        displaySearchResults([
            {
                id: 1,
                first_name: 'John',
                last_name: 'Doe',
                age: 45,
                phone: '+255 123 456 789',
                visit_count: 8,
                last_visit: '2025-09-10',
                risk_factors: ['Hypertension', 'Diabetes']
            },
            {
                id: 2,
                first_name: 'Mary',
                last_name: 'Johnson',
                age: 32,
                phone: '+255 987 654 321',
                visit_count: 3,
                last_visit: '2025-09-14',
                risk_factors: ['Asthma']
            }
        ]);
    }, 1500);
}

function displaySearchResults(patients) {
    let resultsHTML = '';
    
    if (patients.length === 0) {
        resultsHTML = '<div class="text-center text-gray-500 py-8">No patients found matching your criteria</div>';
    } else {
        resultsHTML = '<div class="space-y-4">';
        patients.forEach(patient => {
            const riskBadges = patient.risk_factors.map(factor => 
                `<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">${factor}</span>`
            ).join(' ');
            
            resultsHTML += `
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer hover-lift" 
                     onclick="viewPatientHistory(${patient.id})">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-medium">
                                ${patient.first_name.charAt(0)}${patient.last_name.charAt(0)}
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">${patient.first_name} ${patient.last_name}</h4>
                                <div class="text-sm text-gray-600">
                                    Age: ${patient.age} | Phone: ${patient.phone}
                                </div>
                                <div class="text-sm text-gray-500">
                                    ${patient.visit_count} visits | Last: ${patient.last_visit}
                                </div>
                                <div class="mt-2 space-x-2">
                                    ${riskBadges}
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                View History
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        resultsHTML += '</div>';
    }
    
    document.getElementById('searchResultsContent').innerHTML = resultsHTML;
}

function viewPatientHistory(patientId) {
    currentPatientId = patientId;
    
    // Show loading state
    document.getElementById('patientAnalytics').classList.remove('hidden');
    
    // Simulate loading patient analytics
    setTimeout(() => {
        loadPatientAnalytics(patientId);
    }, 500);
}

function loadPatientAnalytics(patientId) {
    // Update summary cards
    document.getElementById('totalVisits').textContent = '8';
    document.getElementById('totalLabTests').textContent = '15';
    document.getElementById('totalMedications').textContent = '12';
    document.getElementById('riskScore').textContent = 'Medium';
    
    // Load clinical alerts
    displayClinicalAlerts([
        {
            type: 'drug_interaction',
            severity: 'high',
            message: 'Potential interaction between Warfarin and Aspirin',
            action: 'Review medication compatibility'
        },
        {
            type: 'lab_trend',
            severity: 'medium',
            message: 'Blood sugar levels increasing over last 3 visits',
            action: 'Consider diabetes management review'
        }
    ]);
    
    // Load medical timeline
    displayMedicalTimeline();
    
    // Initialize charts
    initializeHealthTrendCharts();
}

function displayClinicalAlerts(alerts) {
    let alertsHTML = '';
    
    if (alerts.length === 0) {
        alertsHTML = '<div class="text-center text-gray-500 py-4">No clinical alerts</div>';
    } else {
        alerts.forEach(alert => {
            const severityColor = alert.severity === 'high' ? 'red' : 
                                 alert.severity === 'medium' ? 'yellow' : 'blue';
            
            alertsHTML += `
                <div class="border-l-4 border-${severityColor}-400 bg-${severityColor}-50 p-4 rounded-r-lg">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-${severityColor}-400"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-${severityColor}-800">${alert.message}</p>
                            <p class="text-sm text-${severityColor}-600 mt-1">${alert.action}</p>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    document.getElementById('clinicalAlerts').innerHTML = alertsHTML;
}

function displayMedicalTimeline() {
    const timelineHTML = `
        <div class="flow-root">
            <ul role="list" class="-mb-8">
                <li>
                    <div class="relative pb-8">
                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></span>
                        <div class="relative flex space-x-3">
                            <div>
                                <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                    <i class="fas fa-stethoscope text-white text-sm"></i>
                                </span>
                            </div>
                            <div class="min-w-0 flex-1 pt-1.5">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Regular Checkup</p>
                                    <p class="text-sm text-gray-500">Sept 14, 2025 - Dr. Smith</p>
                                    <p class="text-sm text-gray-600 mt-1">Blood pressure: 140/90, prescribed Lisinopril</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="relative pb-8">
                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></span>
                        <div class="relative flex space-x-3">
                            <div>
                                <span class="h-8 w-8 rounded-full bg-purple-500 flex items-center justify-center ring-8 ring-white">
                                    <i class="fas fa-flask text-white text-sm"></i>
                                </span>
                            </div>
                            <div class="min-w-0 flex-1 pt-1.5">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Lab Results</p>
                                    <p class="text-sm text-gray-500">Sept 10, 2025</p>
                                    <p class="text-sm text-gray-600 mt-1">Blood sugar: 180 mg/dL (↑ from 150), HbA1c: 7.2%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    `;
    
    document.getElementById('medicalTimeline').innerHTML = timelineHTML;
}

function initializeHealthTrendCharts() {
    // Simple chart simulation (in real implementation, use Chart.js or similar)
    const bpCanvas = document.getElementById('bpTrendChart');
    const labCanvas = document.getElementById('labTrendChart');
    
    if (bpCanvas && labCanvas) {
        // Add chart visualization here
        // For now, just show placeholder text
        bpCanvas.style.display = 'none';
        labCanvas.style.display = 'none';
        
        bpCanvas.parentElement.innerHTML += '<div class="flex items-center justify-center h-full text-gray-500">Blood Pressure: 130/85 → 140/90 → 135/88 (Improving)</div>';
        labCanvas.parentElement.innerHTML += '<div class="flex items-center justify-center h-full text-gray-500">Blood Sugar: 150 → 165 → 180 mg/dL (Trending Up)</div>';
    }
}

function exportPatientData() {
    if (!currentPatientId) {
        showToast('Please select a patient first', 'warning');
        return;
    }
    
    showAdvancedNotification(
        'Export Started',
        'Generating comprehensive patient report...',
        'info',
        [
            { text: 'Download PDF', onclick: 'downloadPDF()' },
            { text: 'Email Report', onclick: 'emailReport()' }
        ]
    );
}

function downloadPDF() {
    showToast('PDF download started', 'success');
}

function emailReport() {
    showToast('Report sent via email', 'success');
}

// Initialize advanced search on page load
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus search input
    const searchInput = document.getElementById('smartSearchInput');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSmartSearch();
            }
        });
    }
});
</script>
