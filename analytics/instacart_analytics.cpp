#include <iostream>
#include <regex>
#include <sqlite3.h>

const std::string dbFile = "../db/applicants.sqlite3";
const std::string query = ""
    " SELECT count(*), min(date(created_at, 'weekday 1')) AS week_start, workflow_state, strftime('%W', created_at) AS week"
    " FROM applicants"
    " WHERE created_at >= :start AND created_at <= :end"
    " GROUP BY week, workflow_state;";

/** 
 * @brief  Prints help menu to std::cerr
 *
 * @param  string script: Name of the script being run
 * @retval None
 */
void displayHelpError(const std::string &script) {
    std::cerr << "Proper usage: " << script << " [yyyy-mm-dd] [yyyy-mm-dd]" << std::endl;
    std::cerr << "\twhere first argument is start date and second is end date" << std::endl;
}

/** 
 * @brief  Validates that the date string being passed in conforms to the YYYY-mm-dd format
 *         NOTE: this does not validate whether or not the date is an actual, possible date
 *
 * @param  string date: Date that we wish to check the format of
 * @retval bool whether or not the 
 */
bool isValidDateFormat(const std::string &date) {
    std::regex dateFormat("^\\d{4}-\\d{2}-\\d{2}$");
    return std::regex_search(date, dateFormat);
}

int main(int argc, char **argv) {
    sqlite3 *db = nullptr;
    sqlite3_stmt *stmt = nullptr;

    // Error out for any of the following reasons:
    // - we don't have enough args
    // - start is after end data
    // - either start or end date are not in yyyy-mm-dd format
    if (argc < 3 || strcmp(argv[1], argv[2]) >= 0 
        || !isValidDateFormat(argv[1]) || !isValidDateFormat(argv[2])
    ) {
        displayHelpError(argv[0]);
        return 1;
    }

    if (SQLITE_OK != sqlite3_open(dbFile.c_str(), &db)) {
        std::cerr << "Can't open database file: " << dbFile << std::endl;
        sqlite3_close(db);
        return 1;
    }

    if (SQLITE_OK != sqlite3_prepare_v2(db, query.c_str(), query.length(), &stmt, nullptr)
        || stmt == nullptr
    ) {
        std::cerr << "Error preparing sqlite3_stmt." << std::endl;
        std::cerr << "Error: " << sqlite3_errmsg(db) << std::endl;
        sqlite3_close(db);
        return 1; 
    }

    // Ensure that we capture the end of the day... Should be much more efficient to handle this here rather than in the SQL query
    std::string endDate(argv[2]);
    endDate += " 23:59:59";
    
    sqlite3_bind_text(stmt, 1, argv[1], strlen(argv[1]), nullptr);
    sqlite3_bind_text(stmt, 2, endDate.c_str(), endDate.length(), nullptr);

    // Generate the CSV output data
    std::cout << "count,week,workflow_state" << std::endl;
    while (SQLITE_ROW == sqlite3_step(stmt)) {
        std::cout << sqlite3_column_int(stmt, 0) << "," << sqlite3_column_text(stmt, 1) << "," << sqlite3_column_text(stmt, 2) << std::endl;
    }

    // Clean up
    sqlite3_finalize(stmt);
    sqlite3_close(db);

    return 0;
}