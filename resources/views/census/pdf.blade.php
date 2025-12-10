<!DOCTYPE html>
<html>
<head>
    <title>Census Snapshot - Barangay Matina Pangi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 16px;
            color: #666;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            background-color: #2c5f2d;
            color: white;
            padding: 8px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .stats-grid {
            display: table;
            width: 100%;
        }
        .stats-item {
            display: table-cell;
            width: 33.33%;
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }
        .stats-value {
            font-size: 24px;
            font-weight: bold;
            color: #2c5f2d;
        }
        .stats-label {
            color: #666;
            font-size: 11px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>BARANGAY MATINA PANGI</h1>
        <h2>Census Snapshot Report</h2>
        <p>{{ $census['generated_at'] }}</p>
    </div>

    <!-- Population Overview -->
    <div class="section">
        <div class="section-title">POPULATION OVERVIEW</div>
        <div class="stats-grid">
            <div class="stats-item">
                <div class="stats-value">{{ number_format($census['total_population']) }}</div>
                <div class="stats-label">Total Population</div>
            </div>
            <div class="stats-item">
                <div class="stats-value">{{ number_format($census['total_households']) }}</div>
                <div class="stats-label">Total Households</div>
            </div>
            <div class="stats-item">
                <div class="stats-value">{{ number_format($census['average_household_size'], 1) }}</div>
                <div class="stats-label">Avg Household Size</div>
            </div>
        </div>
    </div>

    <!-- Demographics -->
    <div class="section">
        <div class="section-title">DEMOGRAPHICS</div>
        <table>
            <tr>
                <th colspan="2">Gender Distribution</th>
                <th colspan="2">Age Groups</th>
            </tr>
            <tr>
                <td>Male</td>
                <td>{{ number_format($census['male_count']) }}</td>
                <td>Children (0-12)</td>
                <td>{{ number_format($census['children_count']) }}</td>
            </tr>
            <tr>
                <td>Female</td>
                <td>{{ number_format($census['female_count']) }}</td>
                <td>Teens (13-19)</td>
                <td>{{ number_format($census['teens_count']) }}</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td>Adults (20-59)</td>
                <td>{{ number_format($census['adults_count']) }}</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td>Seniors (60+)</td>
                <td>{{ number_format($census['seniors_count']) }}</td>
            </tr>
        </table>
    </div>

    <!-- Special Categories -->
    <div class="section">
        <div class="section-title">SPECIAL CATEGORIES</div>
        <table>
            <tr>
                <td>Persons with Disability (PWD)</td>
                <td>{{ number_format($census['pwd_count']) }}</td>
            </tr>
            <tr>
                <td>Senior Citizens</td>
                <td>{{ number_format($census['seniors_count']) }}</td>
            </tr>
            <tr>
                <td>Registered Voters</td>
                <td>{{ number_format($census['voters_count']) }}</td>
            </tr>
            <tr>
                <td>Non-Voters</td>
                <td>{{ number_format($census['non_voters_count']) }}</td>
            </tr>
            <tr>
                <td>4Ps Beneficiaries</td>
                <td>{{ number_format($census['fourps_count']) }}</td>
            </tr>
        </table>
    </div>

    <!-- Civil Status & Employment -->
    <div class="section">
        <div class="section-title">CIVIL STATUS & EMPLOYMENT</div>
        <table>
            <tr>
                <th colspan="2">Civil Status</th>
                <th colspan="2">Employment Status</th>
            </tr>
            <tr>
                <td>Single</td>
                <td>{{ number_format($census['single_count']) }}</td>
                <td>Employed</td>
                <td>{{ number_format($census['employed_count']) }}</td>
            </tr>
            <tr>
                <td>Married</td>
                <td>{{ number_format($census['married_count']) }}</td>
                <td>Unemployed</td>
                <td>{{ number_format($census['unemployed_count']) }}</td>
            </tr>
            <tr>
                <td>Widowed</td>
                <td>{{ number_format($census['widowed_count']) }}</td>
                <td colspan="2"></td>
            </tr>
        </table>
    </div>

    <!-- Housing & Income -->
    <div class="section">
        <div class="section-title">HOUSING & INCOME</div>
        <table>
            <tr>
                <th colspan="2">Housing</th>
                <th colspan="2">Income</th>
            </tr>
            <tr>
                <td>Owned Houses</td>
                <td>{{ number_format($census['owned_houses']) }}</td>
                <td>Total Monthly Income</td>
                <td>₱{{ number_format($census['total_income'], 2) }}</td>
            </tr>
            <tr>
                <td>Rented Houses</td>
                <td>{{ number_format($census['rented_houses']) }}</td>
                <td>Average Monthly Income</td>
                <td>₱{{ number_format($census['average_income'], 2) }}</td>
            </tr>
            <tr>
                <td>With Electricity</td>
                <td>{{ number_format($census['with_electricity']) }}</td>
                <td colspan="2"></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Generated by: {{ $census['generated_by'] }} | {{ $census['generated_at'] }}</p>
        <p>Barangay Matina Pangi Information System</p>
    </div>
</body>
</html>
