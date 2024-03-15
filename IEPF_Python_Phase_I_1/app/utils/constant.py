UPDATED_FOLIOHEADER_QUERY = """
            SELECT 
                i2.cin,
                i2.folionumber AS folio_number,
                fh.urn,
                fh.folio_status,
                i2.firstname AS investor_fn,
                i2.middlename AS investor_mn,
                i2.lastname AS investor_ln,
                i2.investor_name,
                i2.father_firstname AS fs_fname,
                i2.father_middlename AS fs_mname,
                i2.father_lastname AS fs_lname,
                i2.fs_name,
                i2.address,
                i2.country,
                i2.state,
                i2.district,
                i2.pincode,
                (fh.nof_shares + i2.nof_shares) AS nof_shares,
                (fh.total_share_value + i2.amounttransfered) AS total_share_value,
                i2.investmenttype AS investment_type,
                i2.pan,
                i2.aadhar_number AS aadhaar,
                i2.date_of_birth AS dob,
                i2.nominee_name AS nominee,
                i2.joint_holder_name,
                fh.investment_under_litigation,
                fh.unpaid_suspense_ac,
                fh.financial_year,
                fh.ucd_claimed,
                fh.iepf_claimed,
                fh.share_qty_error_flag,
                fh.createdat,
                CURRENT_TIMESTAMP AS modifiedat,
                fh.createdby,
                fh.modifiedby,
                fh.status,
                i2.id AS dm_id
            FROM
                folioheader fh
                    INNER JOIN
                (SELECT 
                    ie.cin,
                        ie.folionumber,
                        ie.firstname,
                        ie.middlename,
                        ie.lastname,
                        dm_.id,
                        (ie.amounttransfered / dm_.dividend_amount) AS nof_shares,
                        (ie.amounttransfered / 1) AS amounttransfered,
                        ie.pan,
                        ie.date_of_birth,
                        ie.aadhar_number,
                        ie.nominee_name,
                        ie.joint_holder_name,
                        CASE
                            WHEN ie.folionumber != '' THEN ie.folionumber
                            ELSE ie.accountnumber
                        END AS urn_key,
                        CONCAT(CASE WHEN ie.firstname is null THEN '' ELSE ie.firstname END, ' ', 
                            CASE WHEN ie.middlename is null THEN ' ' ELSE ie.middlename END, ' ', 
                            CASE WHEN ie.lastname is null THEN '' ELSE ie.lastname END) AS investor_name,
                        ie.father_firstname,
                        ie.father_middlename,
                        ie.father_lastname,
                        CONCAT(CASE WHEN ie.father_firstname is null THEN '' ELSE ie.father_firstname END, ' ', 
                            CASE WHEN ie.father_middlename is null THEN '' ELSE ie.father_middlename END, ' ', 
                            CASE WHEN ie.father_lastname is null THEN '' ELSE ie.father_lastname END) AS fs_name,
                        ie.address,
                        ie.country,
                        ie.state,
                        ie.district,
                        ie.pincode,
                        ie.investmenttype
                FROM
                    iepf2 ie
                JOIN (SELECT 
                    dm.id, dm.proposed_date, dm.dividend_amount, cm.cin
            FROM
                dividend_master dm
            JOIN company_master cm ON cm.security_code = dm.security_code) AS dm_ ON ie.cin = dm_.cin
            AND dm_.proposed_date BETWEEN ie.proposeddateoftransfer_start AND ie.proposeddateoftransfer_end) 
            AS i2 ON fh.urn = i2.urn_key;"""

DELETE_FOLIOHEADER_QUERY = """DELETE FROM folioheader WHERE urn in (SELECT CASE
                                WHEN folionumber != '' THEN folionumber
                                ELSE accountnumber
                            END AS urn_key FROM iepf2);"""

INSERT_FOLIOHEADER_QUERY = """
        INSERT INTO folioheader (cin, folio_number, investor_fn, investor_mn, investor_ln, iepf_claimed, ucd_claimed, folio_status, dm_id, nof_shares,
                    total_share_value, pan, dob, aadhaar, nominee, joint_holder_name, urn, investor_name, fs_fname, fs_mname, fs_lname, fs_name,
                    address, country, state, district, pincode, investment_type, createdat, status)
        SELECT 
               cin, folionumber, firstname, middlename, lastname, 0 as iepf_claimed, 0 as ucd_claimed, 'UCD' as folio_status,
               dm_id, nof_shares, amounttransfered, pan, date_of_birth, aadhar_number, nominee_name, joint_holder_name, urn,
               investor_name, father_firstname, father_middlename, father_lastname, fs_name, address, country, state, district,
               pincode, investmenttype, current_timestamp() AS createdat, 1 as status
        FROM (
                    SELECT  ROW_NUMBER() OVER (PARTITION BY CASE
								WHEN ie.folionumber != '' THEN ie.folionumber
								ELSE ie.accountnumber
							END) AS row_num,
							ie.cin,
                            ie.folionumber,
                            ie.firstname,
                            ie.middlename,
                            ie.lastname,
                            dm_.id AS dm_id,
                            (ie.amounttransfered / dm_.dividend_amount) AS nof_shares,
                            (ie.amounttransfered / 1) AS amounttransfered,
                            ie.pan,
                            ie.date_of_birth,
                            ie.aadhar_number,
                            ie.nominee_name,
                            ie.joint_holder_name,
                            CASE
                                WHEN ie.folionumber != '' THEN ie.folionumber
                                ELSE ie.accountnumber
                            END AS urn,
                            CONCAT(CASE WHEN ie.firstname is null THEN '' ELSE ie.firstname END, ' ', 
                                CASE WHEN ie.middlename is null THEN ' ' ELSE ie.middlename END, ' ', 
                                CASE WHEN ie.lastname is null THEN '' ELSE ie.lastname END) AS investor_name,
                            ie.father_firstname,
                            ie.father_middlename,
                            ie.father_lastname,
                            CONCAT(CASE WHEN ie.father_firstname is null THEN '' ELSE ie.father_firstname END, ' ', 
                                CASE WHEN ie.father_middlename is null THEN '' ELSE ie.father_middlename END, ' ', 
                                CASE WHEN ie.father_lastname is null THEN '' ELSE ie.father_lastname END) AS fs_name,
                            ie.address,
                            ie.country,
                            ie.state,
                            ie.district,
                            ie.pincode,
                            ie.investmenttype
                    FROM
                        iepf2 ie
                    JOIN (SELECT 
                        dm.id, dm.proposed_date, dm.dividend_amount, cm.cin
                    FROM
                        dividend_master dm
                    JOIN company_master cm ON cm.security_code = dm.security_code) AS dm_ ON ie.cin = dm_.cin
                    AND (dm_.proposed_date BETWEEN ie.proposeddateoftransfer_start AND ie.proposeddateoftransfer_end)
                WHERE (CASE WHEN ie.folionumber != '' THEN ie.folionumber ELSE ie.accountnumber END NOT IN (
                        SELECT urn FROM folioheader
                   ))
                ) new_fh 
        WHERE (SELECT count(*) FROM folioheader fh WHERE fh.urn=urn) = 0 AND row_num = 1; """

INSERT_FOLIODIVIDEND_QUERY = """
        INSERT INTO folio_dividend (cin, folio_number, dividend_amount, pd_of_xfer, dm_id, status, urn_key, createdat, source, financial_year) 
        SELECT cin, folionumber, amounttransfered, proposeddateoftransfer, dm_id, 1, urn, CURRENT_TIMESTAMP, 1, financial_year FROM (
            SELECT  ie.cin,
                    ie.folionumber,
                    dm_.id AS dm_id,
                    (ie.amounttransfered / 1) AS amounttransfered,
                    CASE
                        WHEN ie.folionumber != '' THEN ie.folionumber
                        ELSE ie.accountnumber
                    END AS urn,
                    ie.proposeddateoftransfer,
                    ie.financial_year
            FROM
                iepf2 ie
            JOIN (
                    SELECT 
                        dm.id, dm.proposed_date, dm.dividend_amount, cm.cin
                    FROM
                        dividend_master dm
                    JOIN company_master cm ON cm.security_code = dm.security_code
                ) AS dm_ ON ie.cin = dm_.cin
            AND (dm_.proposed_date BETWEEN ie.proposeddateoftransfer_start AND ie.proposeddateoftransfer_end)
        ) AS new_fd;"""

TRUNCATE_IEPF2 = """TRUNCATE iepf2"""

IEPF2_EXCEL_DATA = ['firstname', 'middlename', 'lastname', 'father_firstname',
        'father_middlename', 'father_lastname', 'address', 'country', 'state', 
        'district', 'pincode', 'folionumber', 'accountnumber', 'investmenttype', 
        'amounttransfered', 'proposeddateoftransfer', 'pan', 'date_of_birth',
        'aadhar_number', 'nominee_name', 'joint_holder_name', 'remarks',
        'cin', 'dividentcount', 'log_id']

# ------------------------------------------------------------------------------------------------------------------------------- #

INSERT_MULTIPLE_DIVIDEND = """

    INSERT INTO folioheader (cin, folio_number, investor_fn, investor_mn, investor_ln, iepf_claimed, ucd_claimed, folio_status, dm_id, nof_shares,
        pan, dob, aadhaar, nominee, joint_holder_name, urn, investor_name, fs_fname, fs_mname, fs_lname, fs_name,
        address, country, state, district, pincode, investment_type, createdat, status)
    SELECT
        md.cin AS cin,
        md.folionumber AS folio_number,
        md.firstname AS investor_fn,
        md.middlename AS investor_mn,
        md.lastname, 
        0 as iepf_claimed,
        0 as ucd_claimed,
        'UCD' AS folio_status,
        dm.dm_id AS dm_id,
        (md.amounttransfered/dm.dividend_amount) AS nof_shares,
        md.pan AS PAN,
        md.date_of_birth AS dob,
        md.aadhar_number AS aadhaar,
        md.nominee_name AS nominee,
        md.joint_holder_name AS joint_holder_name, 
        CASE WHEN md.folionumber != '' THEN md.folionumber  ELSE md.accountnumber END AS urn, 
        CONCAT(md.firstname, ' ', md.middlename, ' ', md.lastname) as investor_name, 
        md.father_firstname AS fs_fname, 
        md.father_middlename AS fs_mname,
        md.father_lastname AS fs_lname,
        CONCAT(md.father_firstname, ' ', md.father_middlename, ' ', md.father_lastname) as fs_name,
        md.address AS address,
        md.country AS country, 
        md.state AS state, 
        md.district AS district, 
        md.pincode AS pincode,
        md.investmenttype AS investment_type,
        current_timestamp() AS createdat,
        1 AS status
    FROM multiple_dividend md
    LEFT JOIN (
        SELECT cm.cin AS cm_cin, cm.security_code AS cm_security_code, dm.id as dm_id, dm.dividend_amount 
        FROM company_master cm JOIN dividend_master dm ON cm.security_code = dm.security_code
    ) AS dm ON md.cin = dm.cm_cin
    JOIN (
    SELECT id
        FROM multiple_dividend
        WHERE cin COLLATE utf8mb4_unicode_ci = '{cin}' COLLATE utf8mb4_unicode_ci
        AND log_id = {log_id}
        AND proposeddateoftransfer COLLATE utf8mb4_unicode_ci = '{xfer_date}' COLLATE utf8mb4_unicode_ci
        AND id IN (
            SELECT id
            FROM (
                SELECT IF(folionumber IS NOT NULL, folionumber, accountnumber) AS unique_id,
                    @sum := IF(@unique_id COLLATE utf8mb4_unicode_ci = IF(folionumber IS NOT NULL, folionumber, accountnumber) COLLATE utf8mb4_unicode_ci, @sum, 0) + 1 AS D_No,
                    @unique_id := IF(folionumber IS NOT NULL, folionumber, accountnumber),
                    id
                FROM multiple_dividend,
                    (SELECT @unique_id := '', @sum := 0) vars
                WHERE cin COLLATE utf8mb4_unicode_ci = '{cin}' COLLATE utf8mb4_unicode_ci
                    AND log_id = {log_id}
                    AND proposeddateoftransfer COLLATE utf8mb4_unicode_ci = '{xfer_date}' COLLATE utf8mb4_unicode_ci 
                ORDER BY unique_id
            ) s
            WHERE D_No = {division}
            ORDER BY unique_id
    )
    ) t ON md.id = t.id
    AND dm.dm_id = {dividend_id}
    HAVING urn NOT IN (select urn from folioheader);
    """

UPDATE_MULTIPLE_DIVIDEND = """
         
SELECT distinct
    fh.id,
    i2.cin,
    i2.folionumber AS folio_number,
    fh.urn,
    fh.folio_status,
    i2.firstname AS investor_fn,
    i2.middlename AS investor_mn,
    i2.lastname AS investor_ln,
    i2.investor_name,
    i2.father_firstname AS fs_fname,
    i2.father_middlename AS fs_mname,
    i2.father_lastname AS fs_lname,
    i2.fs_name,
    i2.address,
    i2.country,
    i2.state,
    i2.district,
    i2.pincode,
    (fh.nof_shares + i2.nof_shares) AS nof_shares,
    (fh.total_share_value + i2.amounttransfered) AS total_share_value,
    i2.investmenttype AS investment_type,
    i2.pan,
    i2.aadhar_number AS aadhaar,
    i2.date_of_birth AS dob,
    i2.nominee_name AS nominee,
    i2.joint_holder_name,
    fh.investment_under_litigation,
    fh.unpaid_suspense_ac,
    fh.financial_year,
    fh.ucd_claimed,
    fh.iepf_claimed,
    fh.share_qty_error_flag,
    fh.createdat,
    CURRENT_TIMESTAMP AS modifiedat,
    fh.createdby,
    fh.modifiedby,
    fh.status,
    i2.id AS dm_id
FROM
    folioheader fh
INNER JOIN
    (
        SELECT
            md.cin,
            md.folionumber,
            md.firstname,
            md.middlename,
            md.lastname,
            dm_.id,
            (md.amounttransfered / dm_.dividend_amount) AS nof_shares,
            (md.amounttransfered / 1) AS amounttransfered,
            md.pan,
            md.date_of_birth,
            md.aadhar_number,
            md.nominee_name,
            md.joint_holder_name,
            CASE
                WHEN md.folionumber != '' THEN md.folionumber
                ELSE md.accountnumber
            END AS urn_key,
            CONCAT(
                CASE WHEN md.firstname IS NULL THEN '' ELSE md.firstname END,
                ' ',
                CASE WHEN md.middlename IS NULL THEN ' ' ELSE md.middlename END,
                ' ',
                CASE WHEN md.lastname IS NULL THEN '' ELSE md.lastname END
            ) AS investor_name,
            md.father_firstname,
            md.father_middlename,
            md.father_lastname,
            CONCAT(
                CASE WHEN md.father_firstname IS NULL THEN '' ELSE md.father_firstname END,
                ' ',
                CASE WHEN md.father_middlename IS NULL THEN '' ELSE md.father_middlename END,
                ' ',
                CASE WHEN md.father_lastname IS NULL THEN '' ELSE md.father_lastname END
            ) AS fs_name,
            md.address,
            md.country,
            md.state,
            md.district,
            md.pincode,
            md.investmenttype
        FROM
            multiple_dividend md
        JOIN (
            SELECT
                dm.id,
                dm.proposed_date,
                dm.dividend_amount,
                cm.cin
            FROM
                dividend_master dm
            JOIN company_master cm ON cm.security_code = dm.security_code AND dm.id = {division}
        ) AS dm_ ON md.cin = dm_.cin
        JOIN (
        SELECT id
    FROM multiple_dividend
    WHERE cin COLLATE utf8mb4_unicode_ci = '{cin}' COLLATE utf8mb4_unicode_ci
        AND log_id = {log_id}
        AND proposeddateoftransfer COLLATE utf8mb4_unicode_ci = '{xfer_date}' COLLATE utf8mb4_unicode_ci
        AND id IN (
            SELECT id
            FROM (
                SELECT IF(folionumber IS NOT NULL, folionumber, accountnumber) AS unique_id,
                    @sum := IF(@unique_id COLLATE utf8mb4_unicode_ci = IF(folionumber IS NOT NULL, folionumber, accountnumber) COLLATE utf8mb4_unicode_ci, @sum, 0) + 1 AS D_No,
                    @unique_id := IF(folionumber IS NOT NULL, folionumber, accountnumber),
                    id
                FROM multiple_dividend,
                    (SELECT @unique_id := '', @sum := 0) vars
                WHERE cin COLLATE utf8mb4_unicode_ci = '{cin}' COLLATE utf8mb4_unicode_ci
                    AND log_id = {log_id}
                    AND proposeddateoftransfer COLLATE utf8mb4_unicode_ci = '{xfer_date}' COLLATE utf8mb4_unicode_ci 
                ORDER BY unique_id
            ) s
            WHERE D_No = {division}
            ORDER BY unique_id
        )
        ) t ON md.id = t.id
    ) AS i2 ON fh.urn = i2.urn_key;

"""

DELETE_FOLIOHEADER_BY_MULTIPLE_DIVIDEND = """
    DELETE folioheader
FROM folioheader
JOIN (
    SELECT fh.urn
    FROM folioheader fh
    JOIN multiple_dividend md ON (md.folionumber != '' AND fh.urn = md.folionumber)
        OR (md.folionumber = '' AND fh.urn = md.accountnumber)
    WHERE md.cin COLLATE utf8mb4_unicode_ci = '{cin}'
        AND md.log_id = {log_id}
        AND md.proposeddateoftransfer COLLATE utf8mb4_unicode_ci = '{xfer_date}'
    GROUP BY fh.urn
    HAVING MIN(IF(md.folionumber != '', md.id, NULL)) IS NOT NULL
) subquery ON folioheader.urn = subquery.urn;
        
"""

DELETE_MULTIPLE_DIVIDEND = """
        DELETE md
        FROM multiple_dividend md
        JOIN (
            SELECT id
        FROM multiple_dividend
        WHERE cin COLLATE utf8mb4_unicode_ci = '{cin}' COLLATE utf8mb4_unicode_ci
        AND log_id = {log_id}
        AND proposeddateoftransfer COLLATE utf8mb4_unicode_ci = '{xfer_date}' COLLATE utf8mb4_unicode_ci
        AND id IN (
            SELECT id
            FROM (
                SELECT IF(folionumber IS NOT NULL, folionumber, accountnumber) AS unique_id,
                    @sum := IF(@unique_id COLLATE utf8mb4_unicode_ci = IF(folionumber IS NOT NULL, folionumber, accountnumber) COLLATE utf8mb4_unicode_ci, @sum, 0) + 1 AS D_No,
                    @unique_id := IF(folionumber IS NOT NULL, folionumber, accountnumber),
                    id
                FROM multiple_dividend,
                    (SELECT @unique_id := '', @sum := 0) vars
                WHERE cin COLLATE utf8mb4_unicode_ci = '{cin}' COLLATE utf8mb4_unicode_ci
                    AND log_id = {log_id}
                    AND proposeddateoftransfer COLLATE utf8mb4_unicode_ci = '{xfer_date}' COLLATE utf8mb4_unicode_ci 
                ORDER BY unique_id
            ) s
            WHERE D_No = {division}
            ORDER BY unique_id
    )
        ) t ON md.id = t.id;
"""

INSERT_FOLIO_DIVIDEND = """

INSERT INTO folio_dividend (cin, folio_number, dividend_amount, pd_of_xfer, dm_id, status, urn_key, createdat, source, financial_year)
SELECT md.cin AS cin, md.folionumber AS folio_number, (md.amounttransfered/1) AS dividend_amount, md.proposeddateoftransfer AS pd_of_xfer,
    dm.dm_id AS dm_id, 1 AS status, CASE WHEN md.folionumber != '' THEN md.folionumber ELSE md.accountnumber END AS urn_key,
    CURRENT_TIMESTAMP AS createdat, md.log_id AS source, md.financial_year AS financial_year
FROM multiple_dividend md
LEFT JOIN (
    SELECT cm.cin AS cm_cin, cm.security_code AS cm_security_code, dm.id AS dm_id, dm.dividend_amount
    FROM company_master cm JOIN dividend_master dm ON cm.security_code = dm.security_code
) AS dm ON md.cin = dm.cm_cin
JOIN (
SELECT id
        FROM multiple_dividend
        WHERE cin COLLATE utf8mb4_unicode_ci = '{cin}' COLLATE utf8mb4_unicode_ci
        AND log_id = {log_id}
        AND proposeddateoftransfer COLLATE utf8mb4_unicode_ci = '{xfer_date}' COLLATE utf8mb4_unicode_ci
        AND id IN (
            SELECT id
            FROM (
                SELECT IF(folionumber IS NOT NULL, folionumber, accountnumber) AS unique_id,
                    @sum := IF(@unique_id COLLATE utf8mb4_unicode_ci = IF(folionumber IS NOT NULL, folionumber, accountnumber) COLLATE utf8mb4_unicode_ci, @sum, 0) + 1 AS D_No,
                    @unique_id := IF(folionumber IS NOT NULL, folionumber, accountnumber),
                    id
                FROM multiple_dividend,
                    (SELECT @unique_id := '', @sum := 0) vars
                WHERE cin COLLATE utf8mb4_unicode_ci = '{cin}' COLLATE utf8mb4_unicode_ci
                    AND log_id = {log_id}
                    AND proposeddateoftransfer COLLATE utf8mb4_unicode_ci = '{xfer_date}' COLLATE utf8mb4_unicode_ci 
                ORDER BY unique_id
            ) s
            WHERE D_No = {division}
            ORDER BY unique_id
    )
) t ON md.id = t.id
WHERE dm.dm_id = {dividend_id};
"""

GET_DIVIDEND_LIST = """
    SELECT dm.id,  cm.cin, dm.security_code, dm.year, cm.c_fullname, dm.proposed_date, dm.dividend_amount,
	dm.createdat, dm.modifiedat, dm.createdby, dm.modifiedby, dm.ex_date, dm.record_date, dm.bc_start_date,
	dm.bc_end_date, dm.nd_start_date, dm.nd_end_date, dm.purpose FROM multiple_dividend md JOIN company_master cm ON md.cin = cm.cin
	 JOIN dividend_master dm ON dm.security_code = cm.security_code
	 WHERE dm.proposed_date BETWEEN DATE_ADD(DATE_ADD(md.proposeddateoftransfer, INTERVAL -7 YEAR), INTERVAL -4 MONTH)
							AND DATE_ADD(md.proposeddateoftransfer, INTERVAL -7 YEAR)
							AND dm.security_code = '{security_code}'
							AND cm.cin = '{cin}'
	 GROUP BY id, cin, security_code, year, c_fullname, proposed_date, dividend_amount, createdat, modifiedat,
	 createdby, modifiedby, ex_date, record_date, bc_start_date, bc_end_date, nd_start_date, nd_end_date, purpose
	 ORDER BY id;
"""


GET_MULTIPLE_DIVIDEND = """
                            SELECT id, firstname, middlename, lastname, father_firstname, father_middlename, father_lastname,
                                    address, country, state, district, pincode, folionumber, accountnumber, investmenttype,
                                    amounttransfered, proposeddateoftransfer, cin
                            FROM multiple_dividend
                                WHERE cin COLLATE utf8mb4_unicode_ci = '{cin}' COLLATE utf8mb4_unicode_ci
                                        AND log_id = {log_id}
                                        AND proposeddateoftransfer COLLATE utf8mb4_unicode_ci = '{xfer_date}' COLLATE utf8mb4_unicode_ci
                                        AND id IN (
                                                SELECT id
                                                    FROM (
                                                        SELECT IF(folionumber IS NOT NULL, folionumber, accountnumber) AS unique_id,
                                                                @sum := IF(@unique_id COLLATE utf8mb4_unicode_ci = IF(folionumber IS NOT NULL, folionumber, accountnumber) COLLATE utf8mb4_unicode_ci, @sum, 0) + 1 AS D_No,
                                                                @unique_id := IF(folionumber IS NOT NULL, folionumber, accountnumber),
                                                                id
                                                                FROM multiple_dividend,
                                                                    (SELECT @unique_id := '', @sum := 0) vars
                                                                        WHERE cin COLLATE utf8mb4_unicode_ci = '{cin}' COLLATE utf8mb4_unicode_ci
                                                                            AND log_id = {log_id}
                                                                            AND proposeddateoftransfer COLLATE utf8mb4_unicode_ci = '{xfer_date}' COLLATE utf8mb4_unicode_ci
                                                                                ORDER BY unique_id) s
                                                                            WHERE D_No = {division}
                                                                            ORDER BY unique_id)
                                                                        LIMIT {skip}, {take};
"""

GET_TOTAL_COUNT_MULTIPLE_DIVIDEND = """
SELECT COUNT(*) as total_multidividend, GROUP_CONCAT(id) AS id_list
                            FROM multiple_dividend
                                WHERE cin COLLATE utf8mb4_unicode_ci = '{cin}' COLLATE utf8mb4_unicode_ci
                                        AND log_id = {log_id}
                                        AND proposeddateoftransfer COLLATE utf8mb4_unicode_ci = '{xfer_date}' COLLATE utf8mb4_unicode_ci
                                        AND id IN (
                                                SELECT id
                                                    FROM (
                                                        SELECT IF(folionumber IS NOT NULL, folionumber, accountnumber) AS unique_id,
                                                                @sum := IF(@unique_id COLLATE utf8mb4_unicode_ci = IF(folionumber IS NOT NULL, folionumber, accountnumber) COLLATE utf8mb4_unicode_ci, @sum, 0) + 1 AS D_No,
                                                                @unique_id := IF(folionumber IS NOT NULL, folionumber, accountnumber),
                                                                id
                                                                FROM multiple_dividend,
                                                                    (SELECT @unique_id := '', @sum := 0) vars
                                                                        WHERE cin COLLATE utf8mb4_unicode_ci = '{cin}' COLLATE utf8mb4_unicode_ci
                                                                            AND log_id = {log_id}
                                                                            AND proposeddateoftransfer COLLATE utf8mb4_unicode_ci = '{xfer_date}' COLLATE utf8mb4_unicode_ci
                                                                                ORDER BY unique_id) s
                                                                            WHERE D_No = {division}
                                                                            ORDER BY unique_id)
"""
SET_SQL_SAFE_MODE_OFF ="""
SET SQL_SAFE_UPDATES = 0;
"""
# GET_ALL_MULTIPLE_DIVIDEND_IDS = """
# SELECT GROUP_CONCAT(id) AS id_list
#                             FROM multiple_dividend
#                                 WHERE cin COLLATE utf8mb4_unicode_ci = '{cin}' COLLATE utf8mb4_unicode_ci
#                                         AND log_id = {log_id}
#                                         AND proposeddateoftransfer COLLATE utf8mb4_unicode_ci = '{xfer_date}' COLLATE utf8mb4_unicode_ci
#                                         AND id IN (
#                                                 SELECT id
#                                                     FROM (
#                                                         SELECT IF(folionumber IS NOT NULL, folionumber, accountnumber) AS unique_id,
#                                                                 @sum := IF(@unique_id COLLATE utf8mb4_unicode_ci = IF(folionumber IS NOT NULL, folionumber, accountnumber) COLLATE utf8mb4_unicode_ci, @sum, 0) + 1 AS D_No,
#                                                                 @unique_id := IF(folionumber IS NOT NULL, folionumber, accountnumber),
#                                                                 id
#                                                                 FROM multiple_dividend,
#                                                                     (SELECT @unique_id := '', @sum := 0) vars
#                                                                         WHERE cin COLLATE utf8mb4_unicode_ci = '{cin}' COLLATE utf8mb4_unicode_ci
#                                                                             AND log_id = {log_id}
#                                                                             AND proposeddateoftransfer COLLATE utf8mb4_unicode_ci = '{xfer_date}' COLLATE utf8mb4_unicode_ci
#                                                                                 ORDER BY unique_id) s
#                                                                             WHERE D_No = {division}
#                                                                             ORDER BY unique_id)
# """

