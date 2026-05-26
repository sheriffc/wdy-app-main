<?php

namespace App\Queries;

use Illuminate\Support\Facades\DB;

class LearnerPerformanceQueries {

    private function levelExpr(): string {
        return "CASE WHEN ol_level.item_extra = 'jss' THEN 'JSS'
                     WHEN ol_level.item_extra = 'sss' THEN 'SSS'
                     ELSE 'Primary' END";
    }

    private function scoreExpr(): string {
        return "(COALESCE(lp.assessment_1_score, 0) + COALESCE(lp.assessment_2_score, 0)) / lp.max_score * 100";
    }

    private function baseWhere($districtId, $termOid, $levelLabel): string {
        $w = "lp.deleted_at IS NULL
              AND lp.academic_year = (SELECT academic_year FROM school_academic_year WHERE active = 1 LIMIT 1)
              AND lp.assessment_1_score IS NOT NULL
              AND lp.assessment_2_score IS NOT NULL";

        if ($districtId) $w .= " AND s.district_id = " . intval($districtId);
        if ($termOid)    $w .= " AND lp.term_oid = '" . addslashes($termOid) . "'";
        if ($levelLabel) {
            $map = ['Primary' => "NOT IN ('jss','sss')", 'JSS' => "= 'jss'", 'SSS' => "= 'sss'"];
            if (isset($map[$levelLabel])) {
                $w .= " AND (ol_level.item_extra " . $map[$levelLabel] . " OR ol_level.item_extra IS " .
                      ($levelLabel === 'Primary' ? "NULL)" : "NOT NULL)");
            }
        }
        return $w;
    }

    private function baseJoins(): string {
        return "LEFT JOIN school_group sg    ON sg.uuid  = lp.school_group_uuid
                LEFT JOIN option_list ol_level ON ol_level.list_name = 'school_group_level'
                                               AND ol_level.item_id = sg.school_group_level_oid
                LEFT JOIN school s             ON s.uuid = lp.school_uuid";
    }

    public function summary($districtId, $termOid, $levelLabel) {
        $level  = $this->levelExpr();
        $score  = $this->scoreExpr();
        $joins  = $this->baseJoins();
        $where  = $this->baseWhere($districtId, $termOid, $levelLabel);

        $sql = "
            SELECT level_label,
                   COUNT(*)     AS total_assessed,
                   SUM(CASE WHEN avg_score < 50 THEN 1 ELSE 0 END) AS poor_performers
            FROM (
                SELECT lp.learner_uuid,
                       {$level} AS level_label,
                       AVG({$score}) AS avg_score
                FROM learner_performance lp
                {$joins}
                WHERE {$where}
                GROUP BY lp.learner_uuid, {$level}
            ) sub
            GROUP BY level_label
            ORDER BY FIELD(level_label, 'Primary', 'JSS', 'SSS')
        ";
        return DB::select($sql);
    }

    public function trends($districtId, $termOid, $levelLabel) {
        $level  = $this->levelExpr();
        $score  = $this->scoreExpr();
        $joins  = $this->baseJoins();
        $where  = $this->baseWhere($districtId, $termOid, $levelLabel);

        $sql = "
            SELECT {$level}            AS level_label,
                   lp.term_oid,
                   ROUND(AVG({$score}), 1)        AS avg_score,
                   COUNT(DISTINCT lp.learner_uuid) AS learner_count
            FROM learner_performance lp
            {$joins}
            WHERE {$where}
            GROUP BY {$level}, lp.term_oid
            ORDER BY FIELD({$level}, 'Primary', 'JSS', 'SSS'),
                     FIELD(lp.term_oid, 'first_term', 'second_term', 'third_term')
        ";
        return DB::select($sql);
    }

    public function subjects($districtId, $termOid, $levelLabel) {
        $level  = $this->levelExpr();
        $score  = $this->scoreExpr();
        $joins  = $this->baseJoins();
        $where  = $this->baseWhere($districtId, $termOid, $levelLabel);

        $sql = "
            SELECT {$level}                               AS level_label,
                   lp.subject_oid,
                   COALESCE(ol_sub.item_name, lp.subject_oid) AS subject_name,
                   lp.term_oid,
                   ROUND(AVG({$score}), 1)                AS avg_score,
                   COUNT(DISTINCT lp.learner_uuid)         AS learner_count
            FROM learner_performance lp
            {$joins}
            LEFT JOIN option_list ol_sub ON ol_sub.list_name = 'school_subject'
                                        AND ol_sub.item_id   = lp.subject_oid
            WHERE {$where}
            GROUP BY {$level}, lp.subject_oid, ol_sub.item_name, lp.term_oid
            ORDER BY FIELD({$level}, 'Primary', 'JSS', 'SSS'),
                     COALESCE(ol_sub.display_order, 9999),
                     COALESCE(ol_sub.item_name, lp.subject_oid),
                     FIELD(lp.term_oid, 'first_term', 'second_term', 'third_term')
        ";
        return DB::select($sql);
    }
}
