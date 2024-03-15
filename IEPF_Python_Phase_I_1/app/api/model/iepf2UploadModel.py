from .dbConnection import get_connection
import pandas as pd

from ...utils.constant import *

class IEPF2Model:
    def __init__(self):
        self.engine = get_connection()

    def check_filename(self, file_name):
        query = "SELECT COUNT(*) as is_duplicate FROM excel_log WHERE excel_name = '" + file_name + "' AND status = 1 LIMIT 1;"
        result = pd.read_sql_query(query, self.engine)
        return result.loc[0,'is_duplicate']

    def insert_excel_log(self, excel_log):
        result = excel_log.to_sql(con=self.engine, name='excel_log', if_exists='append', index=False)
        return result

    def insert_excel_data(self, excel_data, cin, file_name):
        # get dividend master data
        query = "SELECT d.id,d.proposed_date,d.dividend_amount,c.cin FROM dividend_master d JOIN company_master c ON d.security_code = c.security_code WHERE c.cin = '"+cin+"';"
        dividend_master = pd.read_sql_query(query, self.engine)
        dividend_master['proposed_date'] = pd.to_datetime(dividend_master['proposed_date'])

        dividend_count = excel_data.apply(lambda row: dividend_master[\
                            (dividend_master['proposed_date'] > row["proposeddateoftransfer_start"]) &\
                                (dividend_master['proposed_date'] < row["proposeddateoftransfer_end"])]['proposed_date'].count(), axis=1)
    
        # add xfer log id, start and end date to excel_data
        if(dividend_count.empty):
            excel_data['dividend_count'] = 0
        else:
            excel_data['dividend_count'] = dividend_count
        
        get_logid_query = "select id from excel_log where excel_name = '" + file_name + "' order by uploadedat desc limit 1;"
        log_id = pd.read_sql_query(get_logid_query, self.engine)['id'].loc[0]
        excel_data['log_id'] = log_id

        # get multidividend data
        multi_dividend = excel_data[excel_data['dividend_count'] > 1]

        multi_dividend_columns = multi_dividend.columns
        drop_columns = ['proposeddateoftransfer_start', 'proposeddateoftransfer_end', 'dividend_count']
        for column in ['remarks', 'investment_under_litigation', 'unpaid_suspense_ac']:
            if column in multi_dividend_columns: drop_columns.append(column)
        multi_dividend = multi_dividend.drop(columns=drop_columns)

        multi_dividend_columns = multi_dividend.columns
        multi_dividend_result = multi_dividend.to_sql(con=self.engine, name='multiple_dividend', if_exists='append', index=False)

        # get singledividend data
        single_dividend = excel_data[excel_data['dividend_count'] == 1]
        single_dividend_result = single_dividend.to_sql(con=self.engine, name='iepf2', if_exists='append', index=False)

        # update excel log
        data_processed = single_dividend_result + multi_dividend_result
        file_type = 'multiple dividend' if (multi_dividend_result > 0) else ('single dividend' if(single_dividend_result > 0) else 'no dividend')

        self.update_excel_log(str(data_processed), file_type,  file_name, '1' if(data_processed > 0) else '0')

        # add rows in iepf2_excel_data
        iepf2_excel_data_columns =  [i for i in IEPF2_EXCEL_DATA if i in excel_data.columns]

        excel_data[iepf2_excel_data_columns].to_sql(con=self.engine, name='iepf2_excel_data', if_exists='append', index=False)
        

        return single_dividend_result, multi_dividend_result

    def update_excel_log(self, data_processed, file_type, file_name, status):
        query = "UPDATE excel_log SET dataprocessed = " + data_processed + ", file_type = '" + file_type + "', status =  " + status  + " WHERE excel_name = '" + file_name + "';"
        with self.engine.connect() as connection:
            result = connection.execute(query)
            return result


    def iepf2_processer(self):
        try: 
            # get updated data from iepf2 and folioheader
            updated_folio_header = pd.read_sql_query(UPDATED_FOLIOHEADER_QUERY, self.engine)
            duplicate = updated_folio_header[updated_folio_header.duplicated(['id'])]
            updated_folio_header.drop_duplicates(subset="id", keep='first', inplace=True)

            # delete rows that need to update
            delete_folioheader_result = self.engine.execute(DELETE_FOLIOHEADER_QUERY)

            # insert updated rows
            update_foliheader_result = updated_folio_header.to_sql(con=self.engine, name='folioheader', if_exists='append', index=False)

            # insert rows that doesn't exist in folioheader
            insert_folioheader_result = self.engine.execute(INSERT_FOLIOHEADER_QUERY)

            # insert rows in foliodividend
            insert_foliodivided_result = self.engine.execute(INSERT_FOLIODIVIDEND_QUERY)

            # truncate iepf2 temp table
            truncate_iepf2_result = self.engine.execute(TRUNCATE_IEPF2)

            return True
        except:
            return False
        

    def multiple_dividend_processor(self, cin,log_id,xfer_date,division,dividend_id):
        try:

            # folio header
            print('===== UPDATE_MULTIPLE_DIVIDEND =====')
            update_multi_dividend_result = pd.read_sql_query(UPDATE_MULTIPLE_DIVIDEND.format(cin= cin,log_id = log_id,xfer_date= xfer_date,division=division,dividend_id=dividend_id), self.engine)
            sql_safemode_off = self.engine.execute(SET_SQL_SAFE_MODE_OFF)
            print('===== DELETE_FOLIOHEADER_BY_MULTIPLE_DIVIDEND =====')
            delete_folioheader_result = self.engine.execute(DELETE_FOLIOHEADER_BY_MULTIPLE_DIVIDEND.format(cin= cin,log_id = log_id,xfer_date= xfer_date,division=division,dividend_id=dividend_id))
            update_foliheader_result = update_multi_dividend_result.to_sql(con=self.engine, name='folioheader', if_exists='append', index=False)
            print('===== INSERT_MULTIPLE_DIVIDEND =====')
            insert_multi_dividend_result = self.engine.execute(INSERT_MULTIPLE_DIVIDEND.format(cin= cin,log_id = log_id,xfer_date= xfer_date,division=division,dividend_id=dividend_id))
            print('===== INSERT_FOLIO_DIVIDEND =====')
            insert_folio_dividend_result = self.engine.execute(INSERT_FOLIO_DIVIDEND.format(cin= cin,log_id = log_id,xfer_date= xfer_date,division=division,dividend_id=dividend_id))
            print('===== DELETE_MULTIPLE_DIVIDEND =====')
            delete_multi_dividend_result = self.engine.execute(DELETE_MULTIPLE_DIVIDEND.format(cin= cin,log_id = log_id,xfer_date= xfer_date,division=division,dividend_id=dividend_id))
            # folio dividend
            return True
        except(ZeroDivisionError):
            return False
        
    def get_multiple_dividend(self, cin, security_code, log_id, xfer_date, division, skip, take):
        return_data ={
            'meta': {
                'skip': skip,
                'take': take
            }
        }
        multiple_dividend = pd.read_sql_query(GET_MULTIPLE_DIVIDEND.format(cin=cin, log_id=log_id, division=division, xfer_date=xfer_date, skip=skip, take=take), self.engine)
        if(skip == 0):
            total_count = pd.read_sql_query(GET_TOTAL_COUNT_MULTIPLE_DIVIDEND.format(cin=cin, log_id=log_id, division=division, xfer_date=xfer_date, skip=skip, take=take), self.engine)
            # print(GET_TOTAL_COUNT_MULTIPLE_DIVIDEND.format(cin=cin, log_id=log_id, division=division, xfer_date=xfer_date, skip=skip, take=take))

            return_data['meta']['total_multidividend'] = total_count.to_dict('records')[0]['total_multidividend']
            return_data['meta']['id_list'] = total_count.to_dict('records')[0]['id_list']
            dividend_master = pd.read_sql_query(GET_DIVIDEND_LIST.format(security_code=security_code, cin=cin), self.engine)
            return_data['dividend_master'] = dividend_master.to_dict('records')
        return_data['multiple_dividend'] = multiple_dividend.to_dict('records')
        return return_data