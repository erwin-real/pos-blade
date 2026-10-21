<x-app-layout>

    <x-slot:topbarTitle>
        Import Products
    </x-slot>

    <div class="body-right">
        <div class="container-fluid">

            <!-- Page Heading -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" aria-current="page">
                        <a href="/products">Products</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Import products</li>
                </ol>
            </nav>

            <!-- Content Row -->
            <div class="row">
                <div class="card shadow" style="margin-left: 12px">
                    <div class="alert alert-danger" style="margin-bottom: 0;">
                        <p>
                            <span class="font-weight-bold">Please read and follow these guides before importing a file:</span>
                            <ol class="">
                                <li>The file must be in <span class="font-weight-bold">.csv</span> or
                                    <span class="font-weight-bold">.xls</span> or
                                    <span class="font-weight-bold">.xlsx</span> file extension.</li>
                                <li>The file must have all the following columns:
                                    <ol>
                                        <li>Name, Description, Price, SRP, Expired at, Stocks, Procurement</li>
                                    </ol>
                                </li>
                                <li>The <span class="font-weight-bold">"Expired at"</span> column in the file must have a date format of
                                    <span class="font-weight-bold">yyyy-mm-dd</span>. Example: 2026-10-15</li>
                                <li>If a product in the file has the same
                                    <span class="font-weight-bold">"Name" and "Description"</span> in the
                                    records of products, the product will only increase in the number of
                                    <span class="font-weight-bold">stocks.</span></li>
                                <li>You can add the image of the product individually after importing the file.</li>
                            </ol>
                        </p>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header py-3 bg-white d-flex flex-sm-row flex-column justify-content-between align-items-sm-center">
                            <h6 class="m-0 font-weight-bold text-primary mb-2 mb-sm-0">
                                <i class="fas fa-file-csv mr-2"></i>Bulk Catalog Import Center
                            </h6>
                            <div class="d-flex flex-wrap" style="gap: 10px;">
                                <button type="button" class="btn btn-outline-primary btn-sm font-weight-bold" id="btn-export-template">
                                    <i class="fas fa-file-download mr-1"></i> Download Blank Template
                                </button>
                                <input type="file" id="csv-file-input" accept=".csv" class="d-none">
                                <button type="button" class="btn btn-success btn-sm font-weight-bold" onclick="document.getElementById('csv-file-input').click()">
                                    <i class="fas fa-search-plus mr-1"></i> Stage CSV for Audit
                                </button>
                            </div>
                        </div>
                        
                        <div class="card-body bg-light border-top d-none" id="import-preview-box">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="font-weight-bold text-gray-800 m-0">Catalog Pre-flight Verification Audit</h5>
                                    <small class="text-muted" id="preview-summary-text">Review staged database transformations below.</small>
                                </div>
                                <button type="button" class="btn btn-primary font-weight-bold px-4 shadow" id="btn-finalize-import">
                                    <i class="fas fa-cloud-upload-alt mr-1"></i> Save Staged Catalog
                                </button>
                            </div>

                            <div class="table-responsive rounded border bg-white" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-align-middle table-hover m-0" id="preview-table">
                                    <thead class="bg-gray-100 text-xs font-weight-bold text-gray-700 text-uppercase sticky-top">
                                        <tr>
                                            <th class="pl-3" style="width: 60px;">Row</th>
                                            <th>Product Details (Name & SKU)</th>
                                            <th>Staged Category & Description</th>
                                            <th class="text-right">Price Matrix (Wholesale / SRP)</th>
                                            <th class="text-center">Stocks Allocation</th>
                                            <th class="text-center">Expiration Date</th> <!-- 👈 Added column header -->
                                            <th class="pr-3 text-center" style="width: 150px;">Validation Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="preview-table-body">
                                        <!-- Dynamic Audit Rows Rendered Natively Here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <style>
        .sticky-top { position: sticky; top: 0; z-index: 10; background: #f8f9fc; }
        .table-align-middle td { vertical-align: middle !important; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let stagedItemsPayload = [];
            let processingHasValidationErrors = false;

            const fileInput = document.getElementById('csv-file-input');
            const previewBox = document.getElementById('import-preview-box');
            const previewBody = document.getElementById('preview-table-body');
            const summaryText = document.getElementById('preview-summary-text');
            const finalizeBtn = document.getElementById('btn-finalize-import');
            const exportTemplateBtn = document.getElementById('btn-export-template');

            if (exportTemplateBtn) {
                exportTemplateBtn.addEventListener('click', function() {
                    const headers = ["name", "desc", "srp", "price", "stocks", "category", "procurement", "expired_at"];
                    const samples = [
                        ["Colgate Ultra Mint", "Toothpaste 100ml", "95.50", "62.00", "120", "Toothpaste", "Oral hygiene and toothpastes", "15", "2028-12-31"],
                        ["Safeguard White Soap", "Bar soap", "45.00", "28.50", "85", "Soap", "Antibacterial bar soaps", "10", ""] // Left blank on purpose
                    ];

                    let csvContent = "data:text/csv;charset=utf-8," 
                        + headers.join(",") + "\n"
                        + samples.map(e => e.join(",")).join("\n");

                    const encodedUri = encodeURI(csvContent);
                    const link = document.createElement("a");
                    link.setAttribute("href", encodedUri);
                    link.setAttribute("download", "pos_bulk_catalog_expiry_template.csv");
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                });
            }

            fileInput.addEventListener('change', function() {
                if (!this.files.length) return;
                const reader = new FileReader();
                reader.onload = function(e) {
                    processRawCSVDataText(e.target.result);
                };
                reader.readAsText(this.files[0]);
            });

            function processRawCSVDataText(text) {
                if (text.startsWith('\uFEFF')) {
                    text = text.substring(1);
                }

                const lines = text.split(/\r\n|\n/);
                if (lines.length < 2 || lines[0].trim() === "") {
                    alert('Processing Blocked: Selected file holds zero readable data rows.');
                    return;
                }

                // Isolate the header text row and parse column targets
                const firstLineRawText = lines[0];
                const headers = firstLineRawText.split(',').map(h => h.toLowerCase().trim().replace(/['"]+/g, ''));
                
                const requiredFields = ['name', 'desc', 'srp', 'price', 'stocks', 'category', 'expired_at'];

                const missingFields = requiredFields.filter(f => !headers.includes(f));
                if (missingFields.length > 0) {
                    alert(`Template Layout Structure Broken!\nMissing mandatory fields: ${missingFields.join(', ')}`);
                    fileInput.value = '';
                    return;
                }

                stagedItemsPayload = [];
                previewBody.innerHTML = "";
                processingHasValidationErrors = false;
                let totalStagedValidRowsCount = 0;

                for (let i = 1; i < lines.length; i++) {
                    let currentLine = lines[i].trim();
                    if (currentLine === "") continue;

                    // FIXED: Swapped out the strict quotation regex split method for a reliable raw comma split
                    let rowCells = currentLine.split(',').map(c => c.trim().replace(/['"]+/g, ''));
                    
                    while (rowCells.length < headers.length) {
                        rowCells.push("");
                    }

                    let rowData = {};
                    headers.forEach((headerName, index) => {
                        rowData[headerName] = rowCells[index] || "";
                    });

                    let name = rowData['name'];
                    let desc = rowData['desc'];
                    let category = rowData['category'];
                    let srp = parseFloat(rowData['srp']);         
                    let price = parseFloat(rowData['price']);     
                    let stocks = parseInt(rowData['stocks']);
                    let procurement = parseInt(rowData['procurement']) || 10; 
                    let expired_at = rowData['expired_at'] ? rowData['expired_at'].trim() : ""; 

                    let integrityErrorsList = [];

                    if (!name) integrityErrorsList.push("Missing Name");
                    if (!desc) integrityErrorsList.push("Missing Code SKU");
                    if (!category) integrityErrorsList.push("Empty Category Name");
                    if (isNaN(srp) || srp < 0) integrityErrorsList.push("Malformed SRP Metric");
                    if (isNaN(price) || price < 0) integrityErrorsList.push("Malformed Cost Price");
                    if (isNaN(stocks) || stocks < 0) integrityErrorsList.push("Negative Stocks Registry");
                    
                    if (expired_at !== "" && isNaN(Date.parse(expired_at))) {
                        integrityErrorsList.push("Invalid Expiration Date Format (Use YYYY-MM-DD)");
                    }

                    let statusBadgeHtml = `<span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i>Valid Record</span>`;
                    if (integrityErrorsList.length > 0) {
                        processingHasValidationErrors = true;
                        statusBadgeHtml = `<span class="badge badge-danger px-2 py-1" style="cursor:help;" title="${integrityErrorsList.join(' | ')}">
                                            <i class="fas fa-exclamation-circle mr-1"></i>${integrityErrorsList.length} Errors
                                        </span>`;
                    } else {
                        totalStagedValidRowsCount++;
                        stagedItemsPayload.push({ 
                            name, desc, srp, price, stocks, category, procurement, expired_at 
                        });
                    }

                    let expiryDisplayHtml = expired_at ? `<span class="font-weight-bold font-mono text-gray-800">${expired_at}</span>` : `<span class="text-muted text-xs">No Expiry</span>`;

                    let auditRowMarkup = `
                        <tr>
                            <td class="pl-3 font-weight-bold text-gray-600 text-center">${i}</td>
                            <td>
                                <span class="font-weight-bold text-gray-800 d-block">${name || 'N/A'}</span>
                                <small class="text-muted d-block text-xs font-mono">SKU/Desc Code: ${desc || 'MISSING'}</small>
                            </td>
                            <td>
                                <span class="font-weight-bold text-primary d-block">${category || 'Unassigned'}</span>
                            </td>
                            <td class="text-right font-mono">
                                <small class="text-muted d-block">Cost Price: ₱${isNaN(price) ? '0.00' : price.toFixed(2)}</small>
                                <span class="font-weight-bold text-success">Retail SRP: ₱${isNaN(srp) ? '0.00' : srp.toFixed(2)}</span>
                            </td>
                            <td class="text-center">
                                <span class="font-weight-bold text-gray-700 d-block">${isNaN(stocks) ? '0' : stocks} Units</span>
                                <small class="text-muted text-xs">Procurement Limit: ${procurement}</small>
                            </td>
                            <td class="text-center">${expiryDisplayHtml}</td>
                            <td class="pr-3 text-center">${statusBadgeHtml}</td>
                        </tr>
                    `;
                    previewBody.insertAdjacentHTML('beforeend', auditRowMarkup);
                }

                previewBox.classList.remove('d-none');
                summaryText.innerHTML = `Audit Resolved: Staged <b class="text-success">${totalStagedValidRowsCount} clean rows</b>.`;
                finalizeBtn.disabled = stagedItemsPayload.length === 0;
                fileInput.value = ''; 
            }

                // ==========================================
                // SECURE BATCH SEND
                // ==========================================
            finalizeBtn.addEventListener('click', function() {
                if (processingHasValidationErrors && !confirm("Warning: Anomalous entries will be filtered out. Proceed with clean elements only?")) {
                    return;
                }

                finalizeBtn.disabled = true;
                const originalText = finalizeBtn.innerHTML;
                finalizeBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-1"></i> Saving Entries...';

                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch('/products/import', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ products: stagedItemsPayload })
                })
                .then(async response => {
                    const data = await response.json();
                    if (!response.ok) throw data;
                    return data;
                })
                .then(data => {
                    alert(data.message || 'Batch uploaded successfully!');
                    window.location.reload();
                })
                .catch(error => {
                    console.error('Batch error:', error);
                    alert('Import Failed: ' + (error.message || 'Server error occurred during processing.'));
                    finalizeBtn.disabled = false;
                    finalizeBtn.innerHTML = originalText;
                });
            });
        });
    </script>




</x-app-layout>
