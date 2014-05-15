<?php
/*
 *  $Id: Db2.php 3884 2008-02-22 18:26:35Z jwage $
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.phpdoctrine.org>.
 */
Doctrine::autoload('Doctrine_Sequence');
/**
 * Doctrine_Sequence_Db2
 *
 * @package     Doctrine
 * @subpackage  Sequence
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.phpdoctrine.org
 * @since       1.0
 * @version     $Revision: 3884 $
 */
class Doctrine_Sequence_Db2 extends Doctrine_Sequence
{
    /**
     * Return the most recent value from the specified sequence in the database.
     * This is supported only on RDBMS brands that support sequences
     * (e.g. Oracle, PostgreSQL, DB2).  Other RDBMS brands return null.
     *
     * @param string $sequenceName
     * @return integer
     * @throws Doctrine_Adapter_Db2_Exception
     */
    public function lastSequenceId($sequenceName)
    {
        $sql = 'SELECT PREVVAL FOR '
             . $this->quoteIdentifier($this->conn->formatter->getSequenceName($sequenceName))
             . ' AS VAL FROM SYSIBM.SYSDUMMY1';

        $stmt   = $this->query($sql);
        $result = $stmt->fetchAll(Doctrine::FETCH_ASSOC);
        if ($result) {
            return $result[0]['VAL'];
        } else {
            return null;
        }
    }

    /**
     * Generate a new value from the specified sequence in the database, and return it.
     * This is supported only on RDBMS brands that support sequences
     * (e.g. Oracle, PostgreSQL, DB2).  Other RDBMS brands return null.
     *
     * @param string $sequenceName
     * @return integer
     * @throws Doctrine_Adapter_Db2_Exception
     */
    public function nextSequenceId($sequenceName)
    {
        $this->_connect();
        $sql = 'SELECT NEXTVAL FOR '
             . $this->quoteIdentifier($this->conn->formatter->getSequenceName($sequenceName))
             . ' AS VAL FROM SYSIBM.SYSDUMMY1';
        $stmt = $this->query($sql);
        $result = $stmt->fetchAll(Doctrine::FETCH_ASSOC);
        if ($result) {
            return $result[0]['VAL'];
        } else {
            return null;
        }
    }

    /**
     * Gets the last ID generated automatically by an IDENTITY/AUTOINCREMENT column.
     *
     * As a convention, on RDBMS brands that support sequences
     * (e.g. Oracle, PostgreSQL, DB2), this method forms the name of a sequence
     * from the arguments and returns the last id generated by that sequence.
     * On RDBMS brands that support IDENTITY/AUTOINCREMENT columns, this method
     * returns the last value generated for such a column, and the table name
     * argument is disregarded.
     *
     * The IDENTITY_VAL_LOCAL() function gives the last generated identity value
     * in the current process, even if it was for a GENERATED column.
     *
     * @param string $tableName OPTIONAL
     * @param string $primaryKey OPTIONAL
     * @return integer
     * @throws Doctrine_Adapter_Db2_Exception
     */
    public function lastInsertId($tableName = null, $primaryKey = null)
    {
        $this->_connect();

        if ($tableName !== null) {
            $sequenceName = $tableName;
            if ($primaryKey) {
                $sequenceName .= "_$primaryKey";
            }
            $sequenceName .= '_seq';
            return $this->lastSequenceId($sequenceName);
        }

        $sql = 'SELECT IDENTITY_VAL_LOCAL() AS VAL FROM SYSIBM.SYSDUMMY1';
        $stmt = $this->query($sql);
        $result = $stmt->fetchAll(Doctrine::FETCH_ASSOC);
        if ($result) {
            return $result[0]['VAL'];
        } else {
            return null;
        }
    }
}