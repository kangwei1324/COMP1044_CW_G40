<?php
    $required_role = 'admin';
    include '../includes/auth_check.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Internships - IRMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <?php include '../components/sidebar_admin.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- header -->
             <?php include '../components/header_admin.php'; ?>

            <div class="content-area">
                <div class="page-header">
                    <h1 class="page-title">Manage Internships (Assessor Assignments)</h1>
                    <button class="btn btn-primary btn-auto" onclick="document.getElementById('addForm').style.display='block'">+ Assign Internship</button>
                </div>

                <!-- Add Form (Hidden by default) -->
                <div class="card collapse-form" id="addForm">
                    <h3 style="margin-bottom: 20px;">Assign Assessor to Student Internship</h3>
                    <form class="form-grid">
                        
                        <!-- Row 1: Student and Assessor Dropdowns -->
                        <div class="form-group">
                            <label>Student</label>
                            <select class="form-control" required>
                                <option value="">Select an Unassigned Student...</option>
                                <option value="STU1001">STU1001 - John Doe</option>
                                <option value="STU1003">STU1003 - Mark Lee</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Assessor (Supervisor)</label>
                            <select class="form-control" required>
                                <option value="">Select an Assessor...</option>
                                <option value="2">Dr. Alan Smith</option>
                                <option value="3">Prof. Sarah Jenkins</option>
                            </select>
                        </div>

                        <!-- Row 2: Company Details -->
                        <div class="form-group form-span-2">
                            <label>Company Name</label>
                            <input type="text" class="form-control" placeholder="e.g. Acme Corporation" required>
                        </div>
                        
                        <!-- Row 3: Timeline -->
                        <div class="form-group">
                            <label>Semester</label>
                            <select class="form-control" required>
                                <option value="1">Semester 1</option>
                                <option value="2" selected>Semester 2</option>
                                <option value="3">Special Semester</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Year</label>
                            <input type="number" class="form-control" placeholder="e.g. 2024" value="2024" required>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary btn-auto" onclick="document.getElementById('addForm').style.display='none'">Cancel</button>
                            <button type="button" class="btn btn-primary btn-auto">Save Assignment</button>
                        </div>
                    </form>
                </div>

                <!-- Data Table -->
                <div class="card">
                    <div class="search-container justify-between">
                        <input type="text" class="form-control max-w-400" placeholder="Search Assignments by Student or Company...">
                        
                        <select class="form-control btn-auto">
                            <!-- Filter dropdown simulation -->
                            <option value="">Filter by Assessor: All</option>
                            <option value="2">Dr. Alan Smith</option>
                        </select>
                    </div>

                    <div class="table-responsive">
                        <table class="irms-table">
                            <thead>
                                <tr class="table-header-row">
                                    <th class="table-header-cell">Student</th>
                                    <th class="table-header-cell">Assessor</th>
                                    <th class="table-header-cell">Company</th>
                                    <th class="table-header-cell">Period</th>
                                    <th class="table-header-cell">Result Status</th>
                                    <th class="table-header-cell text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-body-row">
                                    <td class="table-cell-medium">STU1001 <br><span class="subtitle">John Doe</span></td>
                                    <td class="table-cell">Dr. Alan Smith</td>
                                    <td class="table-cell-muted">TechCorp Inc.</td>
                                    <td class="table-cell font-14">Sem 2, 2024</td>
                                    <td class="table-cell">
                                        <span class="badge badge-muted">Awaiting Marks</span>
                                    </td>
                                    <td class="table-actions-cell">
                                        <a href="#" class="action-edit">Edit Params</a>
                                        <a href="#" class="action-revoke">Unassign</a>
                                    </td>
                                </tr>
                                <tr class="table-body-row">
                                    <td class="table-cell-medium">STU1002 <br><span class="subtitle">Jane Smith</span></td>
                                    <td class="table-cell">Prof. Sarah Jenkins</td>
                                    <td class="table-cell-muted">Cloud Solutions Ltd</td>
                                    <td class="table-cell font-14">Sem 2, 2024</td>
                                    <td class="table-cell">
                                        <span class="badge badge-success">Evaluated (85%)</span>
                                    </td>
                                    <td class="table-actions-cell">
                                        <a href="../assessor/view_result.php?student_id=STU1002" class="action-edit font-semibold">View Result</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
