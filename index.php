<?php

class User
{
    private $name;
    private $groupList = [];

    public function User($name)
    {
        $this->name = $name;
    }

    public function addToGroup(Group $group)
    {
        if ($group->joinUser($this)) {
            $this->groupList[] = $group;
            return true;
        }
        return false;
    }

    public function addToGroupList(array $groupList)
    {
        foreach ($groupList as $group) {
            if ($group instanceof Group) {
                $this->addToGroup($group);
            }
        }
    }

    public function sayToUser($message, User $user)
    {
        $this->say($message);
        /** @var Group $group */
        foreach ($this->groupList as $group) {
            if (in_array($user, $group->getUserList())) {
                $user->notify($message);
                return;
            }
        }
    }

    public function sayToGroup($message, $group = null)
    {
        $this->say($message);
        $userList = [];
        if ($group) {
            if (is_array($group)) {
                $userList = $this->getUserListByGroup($group);
            } else {
                $userList = $this->getUserListByGroup([$group]);
            }
        } else {
            $userList = $this->getUserListByGroup($this->groupList);
        }
        if ($userList) {
            /** @var User $user*/
            foreach ($userList as $user) {
                $user->notify($message);
            }
        }
    }

    private function getUserListByGroup(array $groupList)
    {
        $userList = [];
        /** @var Group $group */
        foreach ($groupList as $group) {
            if ($group instanceof Group) {
                foreach ($group->getUserList() as $user) {
                    if (!in_array($user, $userList) && $user != $this) {
                        $userList[] = $user;
                    }
                }
            }
        }
        return $userList;
    }

    private function say($message)
    {
        echo "> {$this->name}: {$message}" . PHP_EOL;
    }

    public function notify($message)
    {
        echo "<<< {$this->name}: {$message}" . PHP_EOL;
    }
}

class Group
{
    private $userList = [];

    public function getUserList()
    {
        return $this->userList;
    }

    public function joinUser(User $user)
    {
        if (!in_array($user, $this->userList)) {
            $this->userList[] = $user;
            return true;
        }
        return false;
    }
}

//----------------------------------------------------------------------------------------------------------------------

$group1 = new Group();
$group2 = new Group();
$group3 = new Group();

$user1 = new User('Dmitry');
$user2 = new User('Alexander');
$user3 = new User('Boris');
$user4 = new User('Vladimir');
$user5 = new User('Gleb');

$user1->addToGroupList([$group1, $group3]);
$user2->addToGroupList([$group1, $group2, $group3]);
$user3->addToGroupList([$group1, $group2]);
$user4->addToGroupList([$group2, $group3]);
$user5->addToGroup($group2);

echo '<pre>';
$user1->sayToUser('Vlad', $user4);
$user1->sayToUser('Gleb (and silence)', $user5);
$user1->sayToGroup('Group of developers: Alex & Borya', $group1);
$user1->sayToGroup('Hello world! Alex-Borya-Vlad');
echo '</pre>';