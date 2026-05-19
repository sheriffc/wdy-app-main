<?php

namespace App\Queries;

use Illuminate\Support\Facades\DB;

class SchoolFeeding
{
    public function getBySchool(string $schoolUuid): array
    {
        $sql = "
            SELECT
                sf.uuid,
                CASE sf.supply_period_oid
                    WHEN 'first_term'  THEN 'First Term'
                    WHEN 'second_term' THEN 'Second Term'
                    WHEN 'third_term'  THEN 'Third Term'
                    ELSE '—'
                END supply_period,
                COALESCE(sf.received_at, '—') received_at,
                CASE sf.supplied_by_oid
                    WHEN 'gosl'  THEN 'GoSL'
                    WHEN 'plan'  THEN 'PLAN'
                    WHEN 'wfp'   THEN 'WFP'
                    WHEN 'crs'   THEN 'CRS'
                    WHEN 'other' THEN CONCAT('Other: ', COALESCE(sf.supplied_by_other, ''))
                    ELSE '—'
                END supplied_by,
                sf.qty_rice,
                sf.qty_beans,
                sf.qty_gari,
                sf.qty_veg_oil,
                sf.qty_salt,
                sf.updated_at submitted_at
            FROM school_feeding sf
            WHERE sf.school_uuid = ?
              AND sf.deleted_at IS NULL
              AND sf.receives_feeding = 1
            ORDER BY sf.updated_at DESC
        ";
        return DB::select($sql, [$schoolUuid]);
    }

    public function getStockBySchool(string $schoolUuid): array
    {
        $sql = "
            SELECT
                sfs.stock_month,
                sfs.qty_rice,
                sfs.qty_beans,
                sfs.qty_gari,
                sfs.qty_veg_oil,
                sfs.qty_salt,
                sfs.updated_at
            FROM school_feeding_stock sfs
            WHERE sfs.school_uuid = ?
              AND sfs.deleted_at IS NULL
            ORDER BY sfs.stock_month DESC
        ";
        return DB::select($sql, [$schoolUuid]);
    }

    public function secretariatTable($districtId = null): array
    {
        $where = "s.deleted_at IS NULL AND s.active = 1 AND s.receives_feeding = 1";
        if ($districtId) {
            $where .= " AND s.district_id = " . intval($districtId);
        }

        $sql = "
            SELECT
                s.uuid                          school_uuid,
                s.name                          school_name,
                COALESCE(s.town_name, '')       town,
                COALESCE(do.name, '')           district,
                s.lat,
                s.lng,
                sf.received_at                  last_supply_date,
                sfs.qty_rice                    stock_rice,
                sfs.qty_beans                   stock_beans,
                sfs.qty_gari                    stock_gari,
                sfs.qty_veg_oil                 stock_veg_oil,
                sfs.qty_salt                    stock_salt,
                sfs.stock_month,
                sfs.updated_at                  stock_updated_at
            FROM school s
            LEFT JOIN district_office do  ON do.district_id = s.district_id
            LEFT JOIN school_feeding sf   ON sf.uuid = (
                SELECT uuid FROM school_feeding
                WHERE school_uuid = s.uuid AND deleted_at IS NULL AND receives_feeding = 1
                ORDER BY updated_at DESC LIMIT 1
            )
            LEFT JOIN school_feeding_stock sfs ON sfs.uuid = (
                SELECT uuid FROM school_feeding_stock
                WHERE school_uuid = s.uuid AND deleted_at IS NULL
                ORDER BY stock_month DESC LIMIT 1
            )
            WHERE $where
            ORDER BY do.name, s.name
        ";

        return DB::select($sql);
    }

    public function feedingTable($districtId = null)
    {
        $whereClause = "";
        if ($districtId) {
            $whereClause = "AND s.district_id = " . intval($districtId);
        }

        $sql = "
            SELECT
                sf.uuid,
                s.uuid                          school_uuid,
                s.name                          school_name,
                do.name                         district,
                g.name                          chiefdom,
                s.school_education_level_oid,
                sf.receives_feeding,
                CASE sf.supply_period_oid
                    WHEN 'first_term'  THEN 'First Term'
                    WHEN 'second_term' THEN 'Second Term'
                    WHEN 'third_term'  THEN 'Third Term'
                    ELSE NULL
                END                             supply_period,
                sf.received_at,
                CASE sf.supplied_by_oid
                    WHEN 'gosl'  THEN 'GoSL'
                    WHEN 'plan'  THEN 'PLAN'
                    WHEN 'wfp'   THEN 'WFP'
                    WHEN 'crs'   THEN 'CRS'
                    WHEN 'other' THEN CONCAT('Other: ', COALESCE(sf.supplied_by_other, ''))
                    ELSE NULL
                END                             supplied_by,
                sf.qty_rice,
                sf.qty_beans,
                sf.qty_gari,
                sf.qty_veg_oil,
                sf.qty_salt,
                sf.updated_at
            FROM school_feeding sf
            INNER JOIN school s ON s.uuid = sf.school_uuid AND s.deleted_at IS NULL
            LEFT  JOIN district_office do ON do.district_id = s.district_id
            LEFT  JOIN geo g ON g.id = s.chiefdom_id
            WHERE sf.deleted_at IS NULL
              AND sf.receives_feeding = 1
              $whereClause
            ORDER BY do.name, s.name
        ";

        return DB::select($sql);
    }
}
