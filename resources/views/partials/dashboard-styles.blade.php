<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Poppins', sans-serif; }

    .page-header {
        background: linear-gradient(135deg, #6993FF 0%, #4D6FE0 100%);
        border-radius: 18px; padding: 28px 32px; color: #fff;
        box-shadow: 0 10px 30px rgba(105,147,255,.25);
    }
    .page-header h1 { color: #fff; }
    .page-header .text-muted-light { color: rgba(255,255,255,.8) !important; }

    .filter-card { background: #fff; border-radius: 14px; box-shadow: 0 4px 18px rgba(0,0,0,.06); border: none; }
    .section-title { font-weight: 800; color: #181c32; font-size: 16px; margin-bottom: 16px; margin-top: 4px; text-transform: uppercase; letter-spacing: .4px; }
    .dashboard-section { margin-bottom: 40px; }

    .stat-card { background: #fff; border-radius: 16px; border: none; box-shadow: 0 4px 18px rgba(0,0,0,.06); transition: transform .2s ease, box-shadow .2s ease; height: 100%; }
    .stat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 28px rgba(0,0,0,.1); }
    .stat-value { font-size: 26px; font-weight: 800; line-height: 1.1; }
    .stat-label { font-size: 13px; font-weight: 600; color: #7e8299; margin-top: 4px; }

    .modern-card { background: #fff; border-radius: 16px; border: none; box-shadow: 0 4px 18px rgba(0,0,0,.06); }
    .modern-card .card-title { font-weight: 700; font-size: 17px; color: #181c32; }

    .table-modern thead th { border: none; color: #a1a5b7; font-size: 12px; text-transform: uppercase; letter-spacing: .5px; }
    .table-modern td { border-color: #f1f1f4; vertical-align: middle; }
    .table-modern tbody tr:hover { background: #f9f9fb; }
    .table-modern th { white-space: nowrap; }
    .table-modern td.nowrap { white-space: nowrap; }

    .badge-modern { border-radius: 20px; padding: 6px 14px; font-weight: 600; font-size: 12px; }

    .poli-bar-bg { background: #f1f1f4; border-radius: 10px; height: 6px; overflow: hidden; margin-top: 6px; }
    .poli-bar-fill { height: 100%; border-radius: 10px; background: linear-gradient(90deg,#6993FF,#4D6FE0); }

    .quickmenu-card { background: #fff; border-radius: 16px; border: none; box-shadow: 0 4px 18px rgba(0,0,0,.06); padding: 20px; text-decoration: none; display: block; transition: transform .15s ease, box-shadow .15s ease; height: 100%; }
    .quickmenu-card:hover { transform: translateY(-3px); box-shadow: 0 10px 24px rgba(0,0,0,.1); text-decoration: none; }
    .quickmenu-label { font-size: 13px; font-weight: 700; color: #7e8299; text-transform: uppercase; letter-spacing: .4px; }
    .quickmenu-value { font-size: 24px; font-weight: 800; color: #181c32; margin: 6px 0 2px; }
    .quickmenu-sub { font-size: 12px; color: #a1a5b7; }

    .avatar-circle { width: 40px; height: 40px; border-radius: 50%; background: #EEF3FF; color: #6993FF; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 15px; flex-shrink: 0; }

    .btn-expand { background: #f4f6f9; border: none; border-radius: 8px; padding: 6px 12px; font-weight: 600; font-size: 12px; color: #6993FF; cursor: pointer; }
    .btn-expand:hover { background: #EEF3FF; }
    .row-detail { display: none; background: #f9f9fb; }
    .row-detail.show { display: table-row; }
    .table-obat { width: 100%; font-size: 13px; }
    .table-obat th { text-align: left; color: #a1a5b7; font-weight: 600; padding: 4px 12px; }
    .table-obat td { padding: 6px 12px; }

    .data-freshness { display: inline-flex; align-items: center; gap: 6px; background: #F3F6F9; color: #7e8299; font-size: 12px; font-weight: 600; padding: 6px 14px; border-radius: 20px; margin-bottom: 20px; }
    .data-freshness svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; }

    .search-icon-wrap { background: #f4f6f9; border: none; display: flex; align-items: center; padding: 0 14px; }
    .search-icon-wrap svg { width: 16px; height: 16px; stroke: #a1a5b7; fill: none; stroke-width: 2; }
</style>