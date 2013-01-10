<?php
namespace Forms;
class Domains extends \Site\Forms\FormDB {
    private $description;
    private $creator;
    private $date_created;
    private $domain_names;
    private $new_domain_names;
    public function __construct(\Moshpit\Auth\Auth $creator=NULL) {
        //Database
        $this->description = "";
        // Defaults
        //$this->creator = "";
        $this->creator = $creator->getUsername();
        $this->date_created = time(); //now
        
        $this->domain_names = array();
        $this->new_domain_names = array();
        parent::__construct();
        //$this->vardump();
        //echo '<pre>';
        //var_dump($this);
        //echo '</pre>';
    }
    
    private function setNewDomainNames($text) {
        $this->new_domain_names = array();
        //Convert text to array and convert to lowewrcase
        $new_domain_names = 
            array_map(
                'strtolower',
                preg_split(
                    "/\s+/", 
                    $text, 
                    NULL, 
                    PREG_SPLIT_NO_EMPTY
                )
            );
        
        //Unique and sort
        foreach (array_count_values($new_domain_names) as $key => $value) {
            $this->new_domain_names[] = $key;
            if ($value > 1) $this->addError("Duplicate names entered - continuing by correcting input: ".$key." existed ".$value." times");
        }
        sort($this->new_domain_names);
        
        //Remove entries that already exist in list
        foreach (array_intersect($this->new_domain_names, $this->domain_names) as $key => $value) {
            unset($this->new_domain_names[$key]);
            $this->addError("Names already in list - continuing by correcting input: ".$value." exists. Removed ".$key);
        }
    }
    
    public function processForm() {
        if ($this->getValue('new', FALSE, TRUE)) {
            throw new \Errors\Redirection(\Moshpit\Common::getValue($_SERVER, 'REDIRECT_URL'));
        }
        if ($this->getValue('save', FALSE, TRUE)) {
            if ($this->id) 
                $this->loadHostnames($this->id);
            $this->setNewDomainNames($this->getValue('new_domain', ""));
            $this->description  = $this->getValue('description', $this->description);
            
            $upsert = NULL;
            if ($this->id)
                $upsert = "UPDATE";    
            else
                $upsert = "INSERT";
            
            $stmt = $this->db->prepare($this->sql['domains']->$upsert);
            
            $stmt->bindParam("1", $this->description);
            $stmt->bindParam("2", $this->creator);
            $stmt->bindParam("3", $this->date_created);
            if ($this->id) 
                $stmt->bindParam("4", $this->id);

            try {
                $stmt->execute();
                if (null === $this->id) {
                    $this->updateID();
                }
            } catch (\Exception $e) {
                $this->addError($e->getMessage());
            }
            
            
            
            // Add hostnames to hostnames table - if we now have an ID and a list of names
            if ($this->id && !empty($this->new_domain_names)) {
                $hostname = '';
                $stmt2 = $this->db->prepare($this->sql['hostnames']->INSERT);
                
                $stmt2->bindParam("1", $this->id); //domain_id
                $stmt2->bindParam("2", $hostname);
                $stmt2->bindParam("3", $this->description);
                $stmt2->bindParam("4", $this->creator);
                $stmt2->bindParam("5", $this->date_created);
                
                foreach ($this->new_domain_names as $key => $value) {
                    $hostname = $value;
                    try {
                        $stmt2->execute();
                        $this->domain_names[$this->db->lastInsertId()] = $value;
                        unset($this->new_domain_names[$key]);
                    } catch (\Exception $e) {
                        $this->addError($e->getMessage());
                    }
                }
            }
            return TRUE;
        } elseif ($this->getValue('remove', FALSE, TRUE)) {
            $remove_domains = $this->getValue('domain_list');
            if ($remove_domains) {
                $hostname = '';
                $stmt = $this->db->prepare($this->sql['hostnames']->DELETE);
                
                $stmt->bindParam("1", $hostname); //domain_id
                foreach ($remove_domains as $value) {
                    $hostname = $value;
                    try {
                        $stmt->execute();
                    } catch (\Exception $e) {
                        $this->addError($e->getMessage());
                    }
                }
            }
            
            if ($this->id) 
                $this->loadHostnames($this->id);
            $this->new_domain_names = array($this->getValue('new_domain', ""));
            $this->description  = $this->getValue('description', $this->description);
            
            return TRUE;
        }
    }

    protected function addFields() {
        $this->addField("info", "id", $this->id, "ID:");
        $this->addField("info", "date", date('D, d M Y H:i:s', $this->date_created), "Date Created:");
        $this->addField("info", "creator", $this->creator, "Creator:");
        $this->addField("textarea","description",   $this->description,  "Description:");
        $this->addField("textarea", "new_domain", implode("\n", $this->new_domain_names),      "Add Domains:")
                    ->addHelpTip("List of domains separated by spaces or newlines. All input will be converted to lowercase, sorted and uniqued");
        if ($this->id) {    
            $domain_list = $this->addField("select", "domain_list[]", $this->domain_names, "Domains:");
            $domain_list->addAttribute('multiple');
            foreach ($this->domain_names as $id => $domain_name) 
                $domain_list->addOption($id, $domain_name, NULL);
            
            $this->addField("submit", "remove", "Remove Hostnames");
            $this->addField("submit", "delete", "Delete Domain");
        }
        $this->addField("submit", "save", "Save");
        $this->addField("submit", "new", "New");
    }

    protected function generateStatements() {
        $result = array();
        $result["domains"] = new \Connex\SQL(
                'domains', 
                array(
                    'description', 
                    'creator',
                    'date_created'
                ), array('domain_id')
            );
        $result["hostnames"] = new \Connex\SQL(
                'hostnames', 
                array(
                    'domain_id',
                    'hostname',
                    'description',
                    'creator',
                    'date_created'
                ), array('hostname_id')
            );
        $result["hostnames_by_domain_id"] = new \Connex\SQL(
                'hostnames', 
                array(
                    'hostname_id',
                    'hostname'
                ), array('domain_id')
            );
        return $result;
    }

    protected function getById($id) {
        $result = NULL;
        $stmt = $this->db->prepare($this->sql['domains']->SELECT);
        //throw new \Exception($this->sql['domains']->SELECT);
        $stmt->bindParam("1", $id);
        $stmt->execute();
        if ($this->db->errno) {
            $this->addError("Error ".$this->db->error." (".$this->db->errno.")");
        }
        
        $stmt->bindColumn("1", $result);
        $stmt->bindColumn("2", $this->description);
        $stmt->bindColumn("3", $this->creator);
        $stmt->bindColumn("4", $this->date_created);
        $stmt->fetch();
        
        //if id does not exist - redirect to new page
        if (!$result)
            throw new \Errors\Redirection(\Moshpit\Common::getValue($_SERVER, 'REDIRECT_URL'), 302, "ID does not exist");
        
        $this->loadHostnames($id);
        return $result;
    }
    
    private function loadHostnames($id) {
        $stmt = $this->db->prepare($this->sql['hostnames_by_domain_id']->SELECT);
        $stmt->bindParam("1", $id);
        $stmt->execute();
        if ($this->db->errno) {
            $this->addError("Error ".$this->db->error." (".$this->db->errno.")");
        }
        
        foreach ($stmt->fetchAll() as $value)
            $this->domain_names[$value["hostname_id"]] = $value["hostname"];
    }
}

?>
