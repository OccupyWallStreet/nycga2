<?php

	/**
	 * Csv files parser
	 * Converts csv-files to html-tables
	 *
	 * @param type $file File url to parse
	 */
	function su_parse_csv( $file ) {
		$csv_lines = file( $file );
		if ( is_array( $csv_lines ) ) {
			//разбор csv
			$cnt = count( $csv_lines );
			for ( $i = 0; $i < $cnt; $i++ ) {
				$line = $csv_lines[$i];
				$line = trim( $line );
				//указатель на то, что через цикл проходит первый символ столбца
				$first_char = true;
				//номер столбца
				$col_num = 0;
				$length = strlen( $line );
				for ( $b = 0; $b < $length; $b++ ) {
					//переменная $skip_char определяет обрабатывать ли данный символ
					if ( $skip_char != true ) {
						//определяет обрабатывать/не обрабатывать строку
						///print $line[$b];
						$process = true;
						//определяем маркер окончания столбца по первому символу
						if ( $first_char == true ) {
							if ( $line[$b] == '"' ) {
								$terminator = '";';
								$process = false;
							}
							else
								$terminator = ';';
							$first_char = false;
						}

						//просматриваем парные кавычки, опредляем их природу
						if ( $line[$b] == '"' ) {
							$next_char = $line[$b + 1];
							//удвоенные кавычки
							if ( $next_char == '"' )
								$skip_char = true;
							//маркер конца столбца
							elseif ( $next_char == ';' ) {
								if ( $terminator == '";' ) {
									$first_char = true;
									$process = false;
									$skip_char = true;
								}
							}
						}

						//определяем природу точки с запятой
						if ( $process == true ) {
							if ( $line[$b] == ';' ) {
								if ( $terminator == ';' ) {

									$first_char = true;
									$process = false;
								}
							}
						}

						if ( $process == true )
							$column .= $line[$b];

						if ( $b == ($length - 1) ) {
							$first_char = true;
						}

						if ( $first_char == true ) {

							$values[$i][$col_num] = $column;
							$column = '';
							$col_num++;
						}
					}
					else
						$skip_char = false;
				}
			}
		}

		$return = '<table><tr>';

		foreach ( $values[0] as $value ) {
			$return .= '<th>' . $value . '</th>';
		}
		$return .= '</tr>';

		array_shift( $values );

		foreach ( $values as $rows ) {

			$return .= '<tr>';
			foreach ( $rows as $col ) {
				$return .= '<td>' . $col . '</td>';
			}
			$return .= '</tr>';

		}

		$return .= '</table>';

		return $return;
	}

?>