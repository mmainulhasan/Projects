import React, {useState, useEffect} from 'react';
import {Link} from 'react-router-dom';
import axios from 'axios';

function PostList() {
    // Define state variables to store posts and loading status
    const [posts, setPosts] = useState([]);
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        // Fetch data from API endpoint when component mounts
        axios.get(`${process.env.REACT_APP_API_BASE_URL}/posts.php`)
            .then((response) => {
                setPosts(response.data); // Update state with retrieved posts
                setIsLoading(false); // Update loading status
            })
            .catch((error) => {
                console.log(error);
                setIsLoading(false); // Update loading status
            });

    }, []);

    return (
        <div className="container mt-5">
            <h2 className="mb-4">All Posts</h2>
            <div className="row">
                {isLoading ? ( // <- check the value of isLoading to determine whether to show the loading indicator
                    <p>Loading posts...</p>
                ) : (
                    posts.map(post => (
                        <div className="col-md-6" key={post.id}>
                            <div className="card mb-4">
                                <div className="card-body">
                                    <h5 className="card-title">{post.title}</h5>
                                    <p className="card-text">By {post.author} on {new Date(post.publish_date).toLocaleDateString()}</p>
                                    <Link to={`/post/${post.id}`} className="btn btn-primary">Read More</Link>
                                </div>
                            </div>
                        </div>
                    ))
                )}
            </div>
        </div>
    );
}

export default PostList;